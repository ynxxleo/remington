<?php

namespace Tests\Unit;

use App\Http\Controllers\CronController;
use App\Models\BotContract;
use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;

class BotSettlementTest extends TestCase
{
    private $database;
    private $reports;
    private $previousContainer;
    private $previousFacadeApplication;
    private $previousResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Container::getInstance();
        $this->previousFacadeApplication = Facade::getFacadeApplication();
        $this->previousResolver = Model::getConnectionResolver();
        $container = new Container();
        Container::setInstance($container);
        $container->instance('config', new Repository());
        $this->database = new Capsule($container);
        $this->database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $this->database->bootEloquent();
        $container->instance('db', $this->database->getDatabaseManager());
        $container->instance(ResponseFactory::class, new class {
            public function json($data, $status = 200)
            {
                return new JsonResponse($data, $status);
            }
        });
        $this->reports = new class {
            public $exceptions = [];
            public $summaries = [];

            public function info($message, array $context = [])
            {
                $this->summaries[] = ['message' => $message, 'context' => $context];
            }

            public function report($exception)
            {
                $this->exceptions[] = $exception;
            }
        };
        $container->instance(ExceptionHandler::class, $this->reports);
        $container->instance('log', $this->reports);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($container);
        Carbon::setTestNow(Carbon::parse('2026-10-13 12:00:00', 'UTC'));

        $schema = $this->database->getConnection()->getSchemaBuilder();
        $schema->create('general_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('last_cron_run')->nullable();
            $table->timestamps();
        });
        $schema->create('bots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('result_missed');
            $table->decimal('profit_missed', 12, 2);
            $table->timestamps();
        });
        $schema->create('wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('symbol');
            $table->decimal('balance', 18, 2);
            $table->timestamps();
        });
        $schema->create('bot_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('bot_id');
            $table->string('pair');
            $table->decimal('amount', 18, 2);
            $table->decimal('profit', 18, 2)->default(0);
            $table->integer('status')->default(0);
            $table->integer('result')->default(0);
            $table->dateTime('end_date');
            $table->timestamps();
        });
        $this->table('general_settings')->insert(['id' => 1]);
        $this->table('bots')->insert(['id' => 1, 'result_missed' => 1, 'profit_missed' => 10]);
        $this->table('wallets')->insert(['id' => 1, 'user_id' => 1, 'symbol' => 'USDT', 'balance' => 50]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        $this->database->getConnection()->disconnect();
        if ($this->previousResolver) {
            Model::setConnectionResolver($this->previousResolver);
        } else {
            Model::unsetConnectionResolver();
        }
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->previousFacadeApplication);
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    /** @dataProvider fallbackOutcomes */
    public function testExpiredUnassignedBotCompletesInOneRun($outcome, $balance, $profit): void
    {
        $this->table('bots')->update(['result_missed' => $outcome]);
        $id = $this->contract();

        $response = (new CronController())->botResult();

        $this->assertSame(['processed' => 1, 'skipped' => 0, 'failed' => 0], $response->getData(true));
        $contract = BotContract::find($id);
        $this->assertSame(1, $contract->status);
        $this->assertSame($outcome, $contract->result);
        $this->assertEquals($profit, $contract->profit);
        $this->assertEquals($balance, $this->table('wallets')->value('balance'));
        $this->assertNotNull($this->table('general_settings')->value('last_cron_run'));
        $this->assertSame([
            'message' => 'Bot settlement completed',
            'context' => ['processed' => 1, 'skipped' => 0, 'failed' => 0],
        ], $this->reports->summaries[0]);
    }

    public static function fallbackOutcomes(): array
    {
        return [[1, 160, 10], [2, 140, 10], [3, 150, 0]];
    }

    public function testAssignedResultIsPreservedAndRetriesDoNotCreditTwice(): void
    {
        $id = $this->contract(['status' => 2, 'result' => 5, 'profit' => 25]);
        $controller = new CronController();

        $this->assertSame(1, $controller->botResult()->getData(true)['processed']);
        $this->assertSame(0, $controller->botMissed()->getData(true)['processed']);
        $this->assertSame(0, $controller->botResult()->getData(true)['processed']);
        $this->assertSame(2, BotContract::find($id)->result);
        $this->assertEquals(125, $this->table('wallets')->value('balance'));
    }

    public function testLegacyMissedUrlAlsoCompletesBots(): void
    {
        $id = $this->contract();

        $this->assertSame(1, (new CronController())->botMissed()->getData(true)['processed']);
        $this->assertSame(1, BotContract::find($id)->status);
    }

    public function testFutureBotsAreUntouchedAndExactEndTimeIsIncluded(): void
    {
        $future = $this->contract(['end_date' => '2026-10-13 12:01:00']);
        $due = $this->contract(['end_date' => '2026-10-13 12:00:00']);

        $this->assertSame(1, (new CronController())->botResult()->getData(true)['processed']);
        $this->assertSame(0, BotContract::find($future)->status);
        $this->assertSame(1, BotContract::find($due)->status);
        $this->assertEquals(160, $this->table('wallets')->value('balance'));
    }

    public function testMissingWalletReportsFailureAndOtherBotsStillComplete(): void
    {
        $missing = $this->contract(['user_id' => 99]);
        $valid = $this->contract();

        $response = (new CronController())->botResult();

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(['processed' => 1, 'skipped' => 0, 'failed' => 1], $response->getData(true));
        $this->assertSame(0, BotContract::find($missing)->status);
        $this->assertSame(1, BotContract::find($valid)->status);
        $this->assertStringContainsString('Bot contract '.$missing, $this->reports->exceptions[0]->getMessage());
        $this->assertEquals(160, $this->table('wallets')->value('balance'));
    }

    public function testInvalidFallbackDoesNotInventAnOutcome(): void
    {
        $this->table('bots')->update(['result_missed' => 0]);
        $id = $this->contract();

        $response = (new CronController())->botResult();

        $this->assertSame(1, $response->getData(true)['failed']);
        $this->assertSame(0, BotContract::find($id)->status);
        $this->assertEquals(50, $this->table('wallets')->value('balance'));
    }

    public function testWalletCreditRollsBackIfContractSaveFails(): void
    {
        $id = $this->contract();
        $this->database->getConnection()->statement(
            "CREATE TRIGGER reject_settlement BEFORE UPDATE ON bot_contracts BEGIN SELECT RAISE(ABORT, 'save failed'); END"
        );

        $response = (new CronController())->botResult();

        $this->assertSame(1, $response->getData(true)['failed']);
        $this->assertSame(0, BotContract::find($id)->status);
        $this->assertEquals(50, $this->table('wallets')->value('balance'));
        $this->database->getConnection()->statement('DROP TRIGGER reject_settlement');
        $this->assertSame(1, (new CronController())->botResult()->getData(true)['processed']);
        $this->assertEquals(160, $this->table('wallets')->value('balance'));
    }

    public function testAllChunksCompleteWithoutSkippingRows(): void
    {
        for ($i = 0; $i < 105; $i++) {
            $this->contract();
        }

        $this->assertSame(105, (new CronController())->botResult()->getData(true)['processed']);
        $this->assertSame(105, $this->table('bot_contracts')->where('status', 1)->count());
        $this->assertEquals(11600, $this->table('wallets')->value('balance'));
    }

    private function table($name)
    {
        return $this->database->getConnection()->table($name);
    }

    private function contract(array $attributes = [])
    {
        return $this->table('bot_contracts')->insertGetId(array_merge([
            'user_id' => 1, 'bot_id' => 1, 'pair' => 'USDT', 'amount' => 100,
            'end_date' => '2026-10-13 11:59:00',
        ], $attributes));
    }
}
