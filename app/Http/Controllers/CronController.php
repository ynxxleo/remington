<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\BotContract;
use App\Models\CryptoCurrency;
use App\Models\TradeLog;
use App\Models\User;
use Carbon\Carbon;
use App\Models\PracticeLog;
use App\Models\Transaction;
use App\Models\GeneralSetting;
use App\Models\CryptoCurrencyPrice;
use App\Models\Extension;
use App\Models\ForexAccounts;
use App\Models\ForexInvestments;
use App\Models\ForexLogs;
use App\Models\MLM;
use App\Models\ScheduledOrders;
use App\Models\Wallet;
use Illuminate\Support\Arr;
use Http;

class CronController extends Controller
{

    public function view()
    {
        $page_title = 'Cron Settings';
        $bot = Extension::where('id',1)->first();
        $mlm = Extension::where('id',3)->first();
        $forex = Extension::where('id',4)->first();
        return view('admin.setting.cron',compact('page_title','bot','mlm','forex'));
    }
    public function index()
    {
    	$tradeLogs = TradeLog::where('status', 0)->where('in_time', '<', Carbon::now())->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($tradeLogs as $tradeLog)
    	{
			$cryptoRate = getCoinRate($tradeLog->crypto->symbol);
			$user = User::find($tradeLog->user_id);
			$wallet = Wallet::where('user_id',$tradeLog->user_id)->where('symbol',$tradeLog->pair)->first();
			if($tradeLog->result == 0)
			{

				if($tradeLog->hilow == 1)
				{
					if($tradeLog->price_was < $cryptoRate)
					{
						$wallet->balance += $tradeLog->amount + (($tradeLog->amount / 100) * $gnl->profit);
						$wallet->save();

                        $tradeLogAmount = $tradeLog->amount + (($tradeLog->amount / 100) * $gnl->profit);
                        $details = 'Trade ' . $tradeLog->crypto->name . ' ' . "WIN";
                        $this->transactions($wallet, $tradeLogAmount, $details);
                        $tradeLog->result = 1;
					}
					else if($tradeLog->price_was > $cryptoRate) {
                        $tradeLog->result = 2;
                    }else{
                    	$wallet->balance += $tradeLog->amount;
						$wallet->save();

                        $tradeLogAmount = $tradeLog->amount;
                        $details = 'Trade ' . $tradeLog->crypto->name . ' ' .  "Refund";
                        $this->transactions($wallet, $tradeLogAmount, $details);
                        $tradeLog->result = 3;
                    }
				}
                else if($tradeLog->hilow == 2)
                {
                    if($tradeLog->price_was > $cryptoRate)
                    {
                        $wallet->balance += $tradeLog->amount + (($tradeLog->amount / 100) * $gnl->profit);
                        $wallet->save();

                        $tradeLogAmount = $tradeLog->amount + (($tradeLog->amount / 100) * $gnl->profit);
                        $details = 'Trade ' . $tradeLog->crypto->name . ' ' . "WIN";
                        $this->transactions($wallet, $tradeLogAmount, $details);
                        $tradeLog->result = 1;
                    }
                    else if($tradeLog->price_was < $cryptoRate)
                    {
                        $tradeLog->result = 2;
                    }
                    else{
                        $wallet->balance += $tradeLog->amount;
                        $wallet->save();

                        $tradeLogAmount = $tradeLog->amount;
                        $details = 'Trade ' . $tradeLog->crypto->name . ' ' .  "Refund";
                        $this->transactions($wallet, $tradeLogAmount, $details);
                        $tradeLog->result = 3;
                    }
                }
                $tradeLog->status = 1;
                $tradeLog->save();
    		}
    	}
    }

    public function transactions($wallet, $tradeLogAmount, $details)
    {
        $transaction = new Transaction();
        $transaction->user_id = $wallet->user_id;
        $transaction->amount = $tradeLogAmount;
        $transaction->post_balance = $wallet->balance;
        $transaction->trx_type = "+";
        $transaction->details = $details;
        $transaction->trx = getTrx();
        $transaction->save();
    }

    public function store()
    {
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        $apiKey = $gnl->coin_api_key;
        $symbols = CryptoCurrencyPrice::get(['symbol']);

        if($symbols->isNotEmpty())
        {
            $symbolArray = $symbols->groupBy('symbol')->map(function ($item, $key) {
                return collect($item);
            });
            $symbol = Arr::flatten($symbolArray->keys());
            $crypto = implode(",", $symbol);

            $parameters = [
                'symbol' => $crypto
            ];
            $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
            $headers = [
              'Accepts: application/json',
              'X-CMC_PRO_API_KEY:'. '7e4b9ed3-a77c-46c3-adc3-f335766cfe2f'//$apiKey
            ];
            logger('api');
            logger($apiKey);
            $qs = http_build_query($parameters);
            $request = "{$url}?{$qs}";

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $request,
              CURLOPT_HTTPHEADER => $headers,
              CURLOPT_RETURNTRANSFER => 1
            ));
            
            
            $responses = json_decode(curl_exec($curl));
            
            curl_close($curl);
            foreach ($responses->data as  $da) {
                $symbol = $da->symbol;
                $cryptoCurrencyPrice = CryptoCurrencyPrice::where('symbol', $da->symbol)->first();
                if ($cryptoCurrencyPrice) {
                    $cryptoCurrencyPrice->name = $da->name;
                    $cryptoCurrencyPrice->symbol = @$da->symbol;
                    $cryptoCurrencyPrice->one_hour = @$da->quote->USD->percent_change_1h;
                    $cryptoCurrencyPrice->price = @$da->quote->USD->price;
                    $cryptoCurrencyPrice->seven_day = @$da->quote->USD->percent_change_7d;
                    $cryptoCurrencyPrice->market_cap = @$da->quote->USD->market_cap;
                    $cryptoCurrencyPrice->twenty_four = @$da->quote->USD->percent_change_24h;
                    $cryptoCurrencyPrice->volume24h = @$da->quote->USD->volume_24h;
                    $cryptoCurrencyPrice->circulating = @$da->circulating_supply;
                    $cryptoCurrencyPrice->save();
                }
            }
        }
        else{
            $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
            $parameters = [
              'start' => '1',
              'limit' => '10',
              'convert' => 'USD'
            ];

            $headers = [
              'Accepts: application/json',
              'X-CMC_PRO_API_KEY:'. $apiKey
            ];

            $qs = http_build_query($parameters);
            $request = "{$url}?{$qs}";

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $request,
              CURLOPT_HTTPHEADER => $headers,
              CURLOPT_RETURNTRANSFER => 1
            ));

            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            foreach ($response->data as  $da) {
                $cryptoCurrencyPrice = new CryptoCurrencyPrice;
                $cryptoCurrencyPrice->name = $da->name;
                $cryptoCurrencyPrice->symbol = @$da->symbol;
                $cryptoCurrencyPrice->one_hour = @$da->quote->USD->percent_change_1h;
                $cryptoCurrencyPrice->price = @$da->quote->USD->price;
                $cryptoCurrencyPrice->seven_day = @$da->quote->USD->percent_change_7d;
                $cryptoCurrencyPrice->market_cap = @$da->quote->USD->market_cap;
                $cryptoCurrencyPrice->twenty_four = @$da->quote->USD->percent_change_24h;
                $cryptoCurrencyPrice->volume24h = @$da->quote->USD->volume_24h;
                $cryptoCurrencyPrice->circulating = @$da->circulating_supply;
                $cryptoCurrencyPrice->save();
            }
        }
    }

    public function practiceCron()
    {
        $practiceLogs = PracticeLog::where('status', 0)->where('in_time', '<', Carbon::now())->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($practiceLogs as $practiceLog)
        {
            $cryptoRate = getCoinRate($practiceLog->crypto->symbol);
            $user = User::find($practiceLog->user_id);
            if($practiceLog->result == 0)
            {
                if($practiceLog->hilow == 1)
                {
                    if($practiceLog->price_was < $cryptoRate)
                    {
                        $user->practice_balance += $practiceLog->amount + (($practiceLog->amount / 100) * $gnl->profit);
                        $user->save();

                        $practiceLog->result = 1;
                    }
                    else if($practiceLog->price_was > $cryptoRate) {
                        $practiceLog->result = 2;
                    }else{
                        $user->practice_balance += $practiceLog->amount;
                        $user->save();

                        $practiceLog->result = 3;
                    }
                }
                else if($practiceLog->hilow == 2)
                {
                    if($practiceLog->price_was > $cryptoRate)
                    {
                        $user->practice_balance += $practiceLog->amount + (($practiceLog->amount / 100) * $gnl->profit);
                        $user->save();
                        $practiceLog->result = 1;
                    }
                    else if($practiceLog->price_was < $cryptoRate)
                    {
                        $practiceLog->result = 2;
                    }
                    else{
                        $user->practice_balance += $practiceLog->amount;
                        $user->save();
                        $practiceLog->result = 3;
                    }
                }
                $practiceLog->status = 1;
                $practiceLog->save();
            }
        }
    }
    public function scheduledOrdersCron()
    {
        $Logs = ScheduledOrders::where('status', 0)->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($Logs as $Log)
        {
            $marketRate = getCoinRate($Log->market);
            $pairRate = getCoinRate($Log->pair);

            if($Log->account == 1) #practice
            {
                if($Log->type == 1) #rise
                {
                    if($Log->method == 1) #higher_than
                    {
                        if(($marketRate / $pairRate) > $Log->price){
                            $practiceLog = new PracticeLog();
                            $practiceLog->user_id = $Log->user_id;
                            $practiceLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $practiceLog->pair = $Log->pair;
                            $practiceLog->amount = $Log->amount;
                            $practiceLog->duration = $Log->duration;
                            $practiceLog->in_time = $Log->in_time;
                            $practiceLog->hilow = '1';
                            $practiceLog->price_was = getCoinRate($Log->market);
                            $practiceLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    } else {
                        if(($marketRate / $pairRate) < $Log->price){
                            $practiceLog = new PracticeLog();
                            $practiceLog->user_id = $Log->user_id;
                            $practiceLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $practiceLog->pair = $Log->pair;
                            $practiceLog->amount = $Log->amount;
                            $practiceLog->duration = $Log->duration;
                            $practiceLog->in_time = $Log->in_time;
                            $practiceLog->hilow = '1';
                            $practiceLog->price_was = getCoinRate($Log->market);
                            $practiceLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    }
                } else {
                    if($Log->method == 1) #higher_than
                    {
                        if(($marketRate / $pairRate) > $Log->price){
                            $practiceLog = new PracticeLog();
                            $practiceLog->user_id = $Log->user_id;
                            $practiceLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $practiceLog->pair = $Log->pair;
                            $practiceLog->amount = $Log->amount;
                            $practiceLog->duration = $Log->duration;
                            $practiceLog->in_time = $Log->in_time;
                            $practiceLog->hilow = '2';
                            $practiceLog->price_was = getCoinRate($Log->market);
                            $practiceLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    } else {
                        if(($marketRate / $pairRate) < $Log->price){
                            $practiceLog = new PracticeLog();
                            $practiceLog->user_id = $Log->user_id;
                            $practiceLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $practiceLog->pair = $Log->pair;
                            $practiceLog->amount = $Log->amount;
                            $practiceLog->duration = $Log->duration;
                            $practiceLog->in_time = $Log->in_time;
                            $practiceLog->hilow = '2';
                            $practiceLog->price_was = getCoinRate($Log->market);
                            $practiceLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    }
                }
            } else {
                if($Log->type == 1) #rise
                {
                    if($Log->method == 1) #higher_than
                    {
                        if(($marketRate / $pairRate) > $Log->price){
                            $tradeLog = new TradeLog();
                            $tradeLog->user_id = $Log->user_id;
                            $tradeLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $tradeLog->pair = $Log->pair;
                            $tradeLog->amount = $Log->amount;
                            $tradeLog->duration = $Log->duration;
                            $tradeLog->in_time = $Log->in_time;
                            $tradeLog->hilow = '1';
                            $tradeLog->price_was = getCoinRate($Log->market);
                            $tradeLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    } else {
                        if(($marketRate / $pairRate) < $Log->price){
                            $tradeLog = new TradeLog();
                            $tradeLog->user_id = $Log->user_id;
                            $tradeLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $tradeLog->pair = $Log->pair;
                            $tradeLog->amount = $Log->amount;
                            $tradeLog->duration = $Log->duration;
                            $tradeLog->in_time = $Log->in_time;
                            $tradeLog->hilow = '1';
                            $tradeLog->price_was = getCoinRate($Log->market);
                            $tradeLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    }
                } else {
                    if($Log->method == 1) #higher_than
                    {
                        if(($marketRate / $pairRate) > $Log->price){
                            $tradeLog = new TradeLog();
                            $tradeLog->user_id = $Log->user_id;
                            $tradeLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $tradeLog->pair = $Log->pair;
                            $tradeLog->amount = $Log->amount;
                            $tradeLog->duration = $Log->duration;
                            $tradeLog->in_time = $Log->in_time;
                            $tradeLog->hilow = '2';
                            $tradeLog->price_was = getCoinRate($Log->market);
                            $tradeLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    } else {
                        if(($marketRate / $pairRate) < $Log->price){
                            $tradeLog = new TradeLog();
                            $tradeLog->user_id = $Log->user_id;
                            $tradeLog->coin_id = CryptoCurrency::where('symbol',$Log->market)->first()->id;
                            $tradeLog->pair = $Log->pair;
                            $tradeLog->amount = $Log->amount;
                            $tradeLog->duration = $Log->duration;
                            $tradeLog->in_time = $Log->in_time;
                            $tradeLog->hilow = '2';
                            $tradeLog->price_was = getCoinRate($Log->market);
                            $tradeLog->save();
                            $scheduled = ScheduledOrders::where('id', $Log->id)->first();
                            $scheduled->status = '1';
                            $scheduled->save();
                        }
                    }
                }
            }
        }
    }

    public function botResult()
    {
        $bot_contracts = BotContract::where('status', 2)->where('end_date', '<', Carbon::now())->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($bot_contracts as $bot_contract)
        {
			$wallet = Wallet::where('user_id',$bot_contract->user_id)->where('symbol',$bot_contract->pair)->first();
            if($bot_contract->result == 4) {
                $wallet->balance += $bot_contract->amount + $bot_contract->profit;
                $wallet->save();
                $bot_contract->result = 1;
                $bot_contract->status = 1;
                $bot_contract->save();
            } else if($bot_contract->result == 5) {
                $wallet->balance += $bot_contract->amount - $bot_contract->profit;
                $wallet->save();
                $bot_contract->result = 2;
                $bot_contract->status = 1;
                $bot_contract->save();
            } else if($bot_contract->result == 6) {
                $wallet->balance += $bot_contract->amount;
                $wallet->save();
                $bot_contract->result = 3;
                $bot_contract->status = 1;
                $bot_contract->save();
            }
        }
    }
    public function botMissed()
    {
        $bot_contracts = BotContract::where('status', 0)->where('end_date', '>', Carbon::now())->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($bot_contracts as $bot_contract)
        {
            $bot = Bot::where('id',$bot_contract->bot_id)->first();
            if($bot->result_missed == 1) {
                $bot_contract->result = 4;
                $bot_contract->status = 2;
                $bot_contract->profit = $bot_contract->amount * ($bot->profit_missed / 100);
                $bot_contract->save();
            } else if($bot->result_missed == 2) {
                $bot_contract->result = 5;
                $bot_contract->status = 2;
                $bot_contract->profit = $bot_contract->amount * ($bot->profit_missed / 100);
                $bot_contract->save();
            } else if($bot->result_missed == 3) {
                $bot_contract->result = 6;
                $bot_contract->status = 2;
                $bot_contract->profit = '0';
                $bot_contract->save();
            }
        }
    }

    public function ForexResult()
    {
        $forex_logs = ForexLogs::where('status', 2)->where('end_date', '<', Carbon::now())->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($forex_logs as $forex_log)
        {
            $account = ForexAccounts::where('user_id',$forex_log->user_id)->first();
            if($forex_log->result == 4) {
                $account->balance += $forex_log->amount + $forex_log->profit;
                $account->save();
                $forex_log->result = 1;
                $forex_log->status = 1;
                $forex_log->save();
            } else if($forex_log->result == 5) {
                $account->balance += $forex_log->amount - $forex_log->profit;
                $account->save();
                $forex_log->result = 2;
                $forex_log->status = 1;
                $forex_log->save();
            } else if($forex_log->result == 6) {
                $account->balance += $forex_log->amount;
                $account->save();
                $forex_log->result = 3;
                $forex_log->status = 1;
                $forex_log->save();
            }
        }
    }
    public function ForexMissed()
    {
        $forex_logs = ForexLogs::where('status', 0)->where('end_date', '>', Carbon::now())->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($forex_logs as $forex_log)
        {
            $forex = ForexInvestments::where('id',$forex_log->investment_id)->first();
            if($forex->result_missed == 1) {
                $forex_log->result = 4;
                $forex_log->status = 2;
                $forex_log->profit = $forex_log->amount * ($forex->profit_missed / 100);
                $forex_log->save();
            } else if($forex->result_missed == 2) {
                $forex_log->result = 5;
                $forex_log->status = 2;
                $forex_log->profit = $forex_log->amount * ($forex->profit_missed / 100);
                $forex_log->save();
            } else if($forex->result_missed == 3) {
                $forex_log->result = 6;
                $forex_log->status = 2;
                $forex_log->profit = '0';
                $forex_log->save();
            }
        }
    }
    public function mlmRanks()
    {
        $mlms = MLM::get();

        foreach($mlms as $mlm)
        {
            if($mlm->left != null && $mlm->right != null){
                $mlm1A = MLM::where('username',$mlm->left)->first();
                $mlm1B = MLM::where('username',$mlm->right)->first();
                if ($mlm1A->rank == 0 && $mlm1B->rank == 0){
                    $mlm->rank = 1;
                } else if ($mlm1A->rank == 1 && $mlm1B->rank == 1){
                    $mlm->rank = 2;
                } else if ($mlm1A->rank == 2 && $mlm1B->rank == 2){
                    $mlm->rank = 3;
                } else if ($mlm1A->rank == 3 && $mlm1B->rank == 3){
                    $mlm->rank = 4;
                } else if ($mlm1A->rank == 4 && $mlm1B->rank == 4){
                    $mlm->rank = 5;
                } else if ($mlm1A->rank == 5 && $mlm1B->rank == 5){
                    $mlm->rank = 6;
                }
                $mlm->save();
            } else {
                $mlm->rank = 0;
                $mlm->save();
            }
        }
    }
}
