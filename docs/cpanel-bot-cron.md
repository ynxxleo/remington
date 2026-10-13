# Bot completion on cPanel

The fix is on the Git branch `deploy-new-ui-20260827`. These files contain the
runtime changes:

- `app/Http/Controllers/CronController.php`
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
/opt/alt/php82/usr/bin/php artisan optimize:clear
/opt/alt/php82/usr/bin/php artisan bots:settle
```

If Git reports local changes or a diverged branch, retain that output for
diagnosis. Do not discard server changes to force the pull.

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

If a contract fails, check its bot's **Result If Missed** setting and the user's
wallet for the contract's quote currency. Missing wallets, deleted bots, or
invalid result settings are logged and left pending for correction. Other valid
contracts continue processing. Once corrected, the next cron run retries them.

Local regression checks use an isolated in-memory SQLite database:

```sh
php vendor/bin/phpunit tests/Unit/BotSettlementTest.php
```
