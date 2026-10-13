# Bot completion on cPanel

The fix is on the Git branch `deploy-new-ui-20260827`. These files contain the
runtime changes:

- `app/Http/Controllers/CronController.php`
- `app/Http/Controllers/BotController.php`
- `app/Http/Controllers/Admin/ManageBotController.php`
- `app/Models/BotContract.php`
- `database/migrations/2026_10_13_000000_add_funding_wallet_id_to_bot_contracts.php`
- `app/Console/Kernel.php`
- `routes/console.php`
- `resources/views/admin/setting/cron.blade.php`

## Your current server

Your supplied cPanel entries include both bot HTTP jobs, and the scheduler
runs every five minutes. The HTTP jobs discard both responses and errors,
so their presence alone does not establish successful execution.

To deploy through Git, run these commands in cPanel Terminal. The pull must
succeed before running the application commands:

```sh
cd /home/remibctq/my-remington-app
git pull --ff-only origin deploy-new-ui-20260827
/opt/alt/php82/usr/bin/php artisan migrate --path=database/migrations/2026_10_13_000000_add_funding_wallet_id_to_bot_contracts.php --force
/opt/alt/php82/usr/bin/php artisan optimize:clear
/opt/alt/php82/usr/bin/php artisan bots:settle
```

If the pull or migration fails, retain that output for diagnosis before running
settlement. Do not discard server changes to force the pull. The migration only
adds a nullable funding wallet ID to bot contracts; it leaves existing balances
and contracts intact.

The final command should print `Processed: N; skipped: N; failed: N`.
If it fails or reports Bot Trader is not installed, retain the output for
diagnosis before changing the bot HTTP jobs.

Change the existing `schedule:run` cron frequency from `*/5 * * * *` to
`* * * * *`. Keep its command unchanged:

```sh
cd /home/remibctq/my-remington-app && /opt/alt/php82/usr/bin/php artisan schedule:run >> /home/remibctq/my-remington-app/storage/logs/scheduler.log 2>&1
```

Once the manual bot command succeeds, remove the two HTTP cron entries
ending in `/cron/bot/result` and `/cron/bot/missed`. Keep all other entries.
The patched scheduler now runs bot settlement every minute and writes its
command output to `storage/logs/bot-cron.log`. Application errors and the
completion summary are in `storage/logs/laravel.log`; scheduler startup
errors remain in `storage/logs/scheduler.log`.

## Alternative configurations

In cPanel Terminal, from the application directory, run:

```sh
php artisan optimize:clear
```

In cPanel Cron Jobs, run one bot command every minute (`* * * * *`).
Use the **Bot Trader Results** command shown in the application's admin Cron
Settings after uploading the changes. It handles both assigned results and
expired bots using the bot's configured **Result If Missed** and **Profit If
Missed**. The old **Results If Missed** URL also performs the full settlement;
one of these HTTP jobs is sufficient.

For a CLI job, replace the paths below with the PHP CLI binary selected for
your application and the full path to its `artisan` file:

```sh
/path/to/php /home/CPANEL_USER/path/to/application/artisan bots:settle >> /home/CPANEL_USER/bot-cron.log 2>&1
```

If cPanel already runs `php artisan schedule:run` every minute, the updated
Laravel scheduler runs `bots:settle` automatically. A separate bot cron is
unnecessary in that setup. Keep unrelated cron jobs.

The command settles expired contracts, including existing overdue contracts,
without waiting for a second cron run. Repeated bot cron calls do not credit
completed contracts again. The HTTP response includes `processed`, `skipped`,
and `failed`; `processed` counts successful completions. Failures return HTTP
500 and are logged with the contract ID in `storage/logs/laravel.log`.

Each bot run also writes a `Bot settlement completed` summary to that log,
including the three counts. For example:

```text
local.INFO: Bot settlement completed {"processed":1,"skipped":0,"failed":0}
```

If this summary is absent after the scheduled minute, check that the bot cron
is configured and review its command output. The old `local.DEBUG: api`
entries came from the separate crypto-price cron; they do not confirm bot
cron execution. The patch removes the price cron's API-key debug logging.

New contracts record the wallet actually debited when they start. Older FX
markets from `resources/data/fxpairs.json` and contracts with the `stock` quote
marker are funded and settled in USDT; their market quote stays unchanged.
Other older contracts use their quote-currency wallet. A missing wallet is not
replaced with an unrelated currency.

If a contract fails, check its bot's **Result If Missed** setting and the user's
funding wallet. Missing wallets, deleted bots, or
invalid result settings are logged and left pending for correction. Other valid
contracts continue processing. Once corrected, the next cron run retries them.

The supplied server log also identifies contract **1804** as having an invalid
assigned result. It stays pending for review. Inspect its current record before
assigning an outcome, especially if the saved result is already 1, 2, or 3
(completed win/loss/draw codes). Check whether a prior payout occurred before
reassigning it. Pending assigned outcomes use codes 4, 5, and 6. The admin form
now validates those codes and cannot reset an already completed contract.

This command reads contract 1804 without changing it:

```sh
/opt/alt/php82/usr/bin/php artisan tinker --execute='dump(\App\Models\BotContract::whereKey(1804)->get(["id","user_id","symbol","pair","amount","profit","status","result"])->toArray());'
```

Local regression checks use an isolated in-memory SQLite database:

```sh
php vendor/bin/phpunit tests/Unit/BotSettlementTest.php
```
