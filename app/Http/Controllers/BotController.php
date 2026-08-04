<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\Ext\InstallController;
use App\Models\AdminNotification;
use App\Models\Bot;
use App\Models\BotContract;
use App\Models\BotTiming;
use App\Models\CryptoCurrency;
use App\Models\CryptoPair;
use App\Models\Extension;
use App\Models\GeneralSetting;
use App\Models\Platform;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BotController extends Controller
{
    public function index()
    {
        $page_title = 'Bot Trader';
        $product = Extension::where('id',1)->first();
        if(is_file(realpath(__DIR__).'/Admin/Ext/'.$product->product_id.'.lic')){
            return view('user.bot.bot-chart', compact('page_title'));
        } else {
            abort(406);
        }
    }
    
     public function fxindex()
    {
    	$page_title = "Fx Pairs List";
    	$empty_message = "No Data Found";
        $jsonString = file_get_contents(resource_path('/data/fxpairs.json'));
        $fxpairs = json_decode($jsonString, false)->data;
        $gnl = GeneralSetting::where('id',1)->first();
    	return view('user.bot.bot-fx-chart', compact('fxpairs', 'page_title','gnl','empty_message'));
    }
     public function stindex()
    {
    	$page_title = "Stocks List";
    	$empty_message = "No Data Found";
        $jsonString = file_get_contents(resource_path('/data/stocks.json'));
        $stocks = json_decode($jsonString, false)->data;
        $gnl = GeneralSetting::where('id',1)->first();
    	return view('user.bot.bot-sk-chart', compact('stocks', 'page_title','gnl','empty_message'));
    }
    public function dash()
    {        
        $page_title = 'Dashboard';
        $user = Auth::user();
        $pageObj = array(
            "url"=>"/user/bot/market",
            "btn_name"=>"New Bot"
            );
            
        //Fetch FX pairs
        $jsonString = file_get_contents(resource_path('/data/fxpairs.json'));
        $fxpairs = json_decode($jsonString, true);
        $fxsymbols = [];
        $crypto = true;
        foreach ($fxpairs['data'] as $key => $entry) {
            array_push($fxsymbols,$entry['symbol']);
        }
        
        $platform = Platform::where('id',1)->first();
        $empty_message = "No Bots Started";
        $bot_contracts = BotContract::where('user_id',$user->id)->whereNotIn('symbol',$fxsymbols)->latest()->paginate(getPaginate(10));
        $bot_contracts_count = BotContract::where('user_id',$user->id)->whereNotIn('symbol',$fxsymbols)->get();
        $profit = $bot_contracts_count->where('result',1)->sum('profit');
        $gnl = GeneralSetting::where('id',1)->first();

        return view('user.bot.bot-dash', compact('page_title','empty_message','gnl','bot_contracts','profit','platform','bot_contracts_count','pageObj','crypto'));
    }

    
    public function fxdash()
    {        
        $page_title = 'Dashboard';
        $user = Auth::user();
        $platform = Platform::where('id',1)->first();
        $empty_message = "No Bots Started";
        $crypto = false;
        $pageObj = array(
            "url"=>"/user/fxbot",
            "btn_name"=>"New FX Bot"
            );
        $jsonString = file_get_contents(resource_path('/data/fxpairs.json'));
        $fxpairs = json_decode($jsonString, true);
        $symbols = [];
        foreach ($fxpairs['data'] as $key => $entry) {
            array_push($symbols,$entry['symbol']);
        }
        $bot_contracts = BotContract::where('user_id',$user->id)->whereIn('symbol',$symbols)->latest()->paginate(getPaginate(10));
        $bot_contracts_count = BotContract::where('user_id',$user->id)->whereIn('symbol',$symbols)->get();
        $profit = $bot_contracts_count->where('result',1)->sum('profit');
        $gnl = GeneralSetting::where('id',1)->first();
        return view('user.bot.bot-dash', compact('page_title','empty_message','gnl','bot_contracts','profit','platform','bot_contracts_count','pageObj','crypto'));
    }
    
        public function stdash()
    {        
        $page_title = 'Dashboard';
        $user = Auth::user();
        $platform = Platform::where('id',1)->first();
        $empty_message = "No Bots Started";
        $crypto = false;
        $pageObj = array(
            "url"=>"/user/stbot",
            "btn_name"=>"New Stock / Bot"
            );
        $jsonString = file_get_contents(resource_path('/data/stocks.json'));
        $stocks = json_decode($jsonString, true);
        $symbols = [];
        foreach ($stocks['data'] as $key => $entry) {
            array_push($symbols,$entry['symbol']);
        }
        $wallets = 'null';
        if(Wallet::where('user_id',$user->id)->whereIn('symbol',$symbols)->exists()){
            $wallets = Wallet::where('user_id',$user->id)->whereIn('symbol',$symbols)->get();
        }
        $bot_contracts = BotContract::where('user_id',$user->id)->whereIn('symbol',$symbols)->latest()->paginate(getPaginate(10));
        $bot_contracts_count = BotContract::where('user_id',$user->id)->whereIn('symbol',$symbols)->get();
        $profit = $bot_contracts_count->where('result',1)->sum('profit');
        $gnl = GeneralSetting::where('id',1)->first();
        return view('user.bot.bot-skdash', compact('page_title','empty_message','gnl','bot_contracts','profit','platform','bot_contracts_count','pageObj','crypto','wallets'));
    }

    public function bot($symbol, $pair)
    {
        
    	$empty_message = "No Data Found";
    	$currency = CryptoCurrency::where('symbol', $symbol)->firstOrFail();
        $pairs = CryptoPair::where('symbol', $pair)->firstOrFail();
    	$page_title = "Bot Trader With " . $symbol.$pair;
    	$route = route('user.home.bot');
        $user = Auth::user();
        $crypto = true;
        $walletpair = $pair;
        if(Wallet::where('user_id', $user->id)->where('symbol', $walletpair)->exists()){
            $wallet = Wallet::where('user_id', $user->id)->where('symbol', $walletpair)->first();
        } else {
            $wallet = 'null';
        }
        $bot_timing = BotTiming::where('status',1)->get();
        $bot_type = Bot::where('status',1)->get();
        if(BotContract::where('user_id',$user->id)->where('symbol',$symbol)->where('status','!=','1')->exists()){
            $runningBot = BotContract::where('user_id',$user->id)->where('symbol',$symbol)->where('status','!=','1')->first();
        } else {
            $runningBot = "null";
        }
        return view('user.bot.bot-chart', compact('page_title','empty_message','bot_timing','bot_type','runningBot', 'currency','pair', 'symbol','pairs','wallet','route','walletpair','crypto'));
    }

    public function fxbot($symbol, $pair)
    {
        
    	$empty_message = "No Data Found";
    	$page_title = "Bot Trader With " . $symbol.$pair;
    	$route = route('user.home.fxbot');
        $user = Auth::user();
        $crypto = false;
        $walletpair = 'USDT';
        if(Wallet::where('user_id', $user->id)->where('symbol', $walletpair)->exists()){
            $wallet = Wallet::where('user_id', $user->id)->where('symbol', $walletpair)->first();
        } else {
            $wallet = 'null';
        }
        $bot_timing = BotTiming::where('status',1)->get();
        $bot_type = Bot::where('status',1)->get();
        if(BotContract::where('user_id',$user->id)->where('symbol',$symbol)->where('status','!=','1')->exists()){
            $runningBot = BotContract::where('user_id',$user->id)->where('symbol',$symbol)->where('status','!=','1')->first();
        } else {
            $runningBot = "null";
        }
        return view('user.bot.bot-chart', compact('page_title','empty_message','bot_timing','bot_type','runningBot','pair', 'symbol','wallet','route','walletpair','crypto'));
    }
    
    public function buystock(Request $request)
    {
        
        $validate = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'symbol' => 'required',
        ]);

        if ($validate->fails()) {
            $notify[] = ['warning', 'Please select a stock and enter amount.'];
            return back()->withNotify($notify);
        }
        
        $user = Auth::user();
        $wallet = Wallet::where('user_id',$user->id)->where('symbol', 'USDT')->first();
        $general = GeneralSetting::first();
        if(($request->amount * $request->shareprice) > $wallet->balance){
            $notify[] = ['warning', 'Your Account Balance '.getAmount($wallet->balance) . 'USDT Not Enough! Please Deposit Money'];
            return back()->withNotify($notify);
        }
        $wallet->balance -= $request->amount * $request->shareprice;
        $wallet->save();
        if( Wallet::where('user_id',$user->id)->where('symbol', $request->symbol)->exists()){
         $wallet = Wallet::where('user_id',$user->id)->where('symbol', $request->symbol)->first();
         $wallet->balance += $request->amount;
         $wallet->save();
         $notify[] = ['success', 'Your Stock Purchased Successfully'];
        return back()->withNotify($notify);
        }
        else{
        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->address = grs(34);
        $wallet->symbol = $request->symbol;
        $wallet->balance = $request->amount;
        $wallet->save();
        $notify[] = ['success', 'Your Stock Purchased Successfully'];
        return back()->withNotify($notify);    
        }
        
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'botTime' => 'required|exists:bot_timings,duration',
            'bot_id' => 'required|exists:bots,id',
        ]);

        if ($validate->fails()) {
            $notify[] = ['warning', 'Please select bot and time duration.'];
            return back()->withNotify($notify);
        }
        $crypto = CryptoCurrency::find($request->coinId);
        $user = Auth::user();
        $wallet = Wallet::where('user_id',$user->id)->where('symbol', $request->walletpair)->first();
        if (! $wallet) {
            $wallet = Wallet::where('user_id',$user->id)->where('symbol', 'USDT')->first();
        }
        // if (!$wallet) {
        //     $wallet = new Wallet();
        //     $wallet->user_id = $user->id;
        //     $wallet->address = grs(34);
        //     $wallet->symbol = $request->walletpair;
        //     $wallet->save();
        // }
        $bot = Bot::where('id',$request->bot_id)->first();
        $general = GeneralSetting::first();
        if(!$wallet || $request->amount > $wallet->balance){
            $notify[] = ['warning', 'Your Account Balance '.getAmount($wallet->balance ?? 0) . ' ' . $general->cur_text .' Not Enough! Please Deposit Money'];
            return back()->withNotify($notify);
        }
        $wallet->balance -= $request->amount;
        $wallet->save();

        $bot_contract = new BotContract();
        $bot_contract->user_id = $user->id;
        $bot_contract->bot_id = $request->bot_id;
        $bot_contract->bot_name = $bot->title;
        $bot_contract->symbol = $request->symbol;
        $bot_contract->pair = $request->pair;
        $bot_contract->amount = $request->amount;
        $bot_contract->min_profit = $bot->min_profit;
        $bot_contract->max_profit = $bot->max_profit;
        $bot_contract->status = '0';
        $bot_contract->start_price = getCoinRate($request->symbol);
        if($request->type == "Min")
        {
            $time = Carbon::now()->addMinutes($request->botTime);
            $duration = $request->botTime * 60;
        }
        elseif($request->type == "Hour")
        {
            $time = Carbon::now()->addHours($request->botTime);
            $duration = $request->botTime * 60 * 60;
        }
        elseif($request->type == "Day")
        {
            $time = Carbon::now()->addDays($request->botTime);
            $duration = $request->botTime * 60 * 60 * 24;
        }

        $bot_contract->duration = $duration;
        $bot_contract->start_date = Carbon::now();
        $bot_contract->end_date = $time;
        $bot_contract->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New bot subscription from '.$user->username;
        $adminNotification->click_url = route('admin.bot.log.pending');
        $adminNotification->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $bot_contract->amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->trx_type = "-";
        $transaction->details = 'Bot contract on ' . $request->symbol . $request->pair;
        $transaction->trx = getTrx();
        $transaction->save();

        $notify[] = ['success', 'Your Contract Started Successfully'];
        return back()->withNotify($notify);
    }
}

