<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bot;
use App\Models\BotContract;
use App\Models\BotTiming;
use App\Models\User;
use Illuminate\Http\Request;



use App\Models\CryptoCurrency;
use App\Models\TradeLog;
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



class ManageBotController extends Controller
{
    public function index()
    {
        $page_title = 'Bot Manager';
        $bots = Bot::paginate(getPaginate(10));
        return view('admin.bot.index', compact('page_title','bots'));
    }

    public function indexTime($id)
    {
        $page_title = 'Bot Manager';
        $bot = Bot::where('id',$id)->first();
        $empty_message = "No Timing Found";
        $bot_timings = BotTiming::where('bot_id',$id)->paginate(getPaginate(10));
        return view('admin.bot.time', compact('page_title','bot_timings','bot','empty_message'));
    }

    public function new()
    {
        $page_title = 'Bot Manager';
        return view('admin.bot.new', compact('page_title'));
    }
    public function edit($id)
    {
        $bot = Bot::findOrFail($id);
        $limits = json_decode($bot->limits);
        $page_title = 'Bot Manager';
        return view('admin.bot.edit', compact('page_title','bot','limits'));
    }
    public function store(Request $request)
    {
        $request->validate([
    		'title' => 'required|max:80',
    		'desc' => 'required|max:500',
    		'perc' => 'required|numeric',
    		'fake' => 'required|numeric',
    		'profit_missed' => 'required|numeric',
    		'result_missed' => 'required',
    		'min_profit' => 'required|numeric',
    		'max_profit' => 'required|numeric',
    		'image' => 'mimes:jpeg,jpg,png,svg'
    	]);

        $bot = new Bot();
        $bot->title = $request->title;
        $bot->desc = $request->desc;
        $bot->perc = $request->perc;
        $bot->fake = $request->fake;
        $bot->profit_missed = $request->profit_missed;
        $bot->result_missed = $request->result_missed;
        $bot->min_profit = $request->min_profit;
        $bot->max_profit = $request->max_profit;
        $bot->limits = json_encode([
            'min_bot_amount' => $request->min_bot_amount,
            'max_bot_amount' => $request->max_bot_amount,
        ]);

        $request->merge(['is_new' => isset($request->is_new) ? 1 : 0]);
        $request->merge(['status' => isset($request->status) ? 1 : 0]);
        $bot->status = $request->status;
        $bot->is_new = $request->is_new;

        $path = imagePath()['bot']['path'];
        $size = imagePath()['bot']['size'];
        if ($request->hasFile('image')) {
            try {
                $filename = uploadImage($request->image, $path, $size);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
            $bot->image = $filename;
        }

        $bot->save();

        $notify[] = ['success', 'Bot has been Updated'];
        return redirect()->route('admin.bot.index')->withNotify($notify);
    }
    public function set(Request $request)
    {
        $bot = BotContract::findOrFail($request->bot_id);
        $bot->profit = $request->profit;
        $bot->result = $request->result;
        $bot->status = '2';
        $bot->save();

        $notify[] = ['success', 'Profit has been adjusted successfully'];
        return back()->withNotify($notify);
    }
    public function storeTime(Request $request)
    {
        $request->validate([
    		'duration' => 'required|numeric'
    	]);
        $bot = Bot::where('id',$request->id)->first();
        $bot_timing = new BotTiming();
        $bot_timing->bot_id = $request->id;
        $bot_timing->duration = $request->duration;
        $bot_timing->type = $request->type;
        $request->merge(['status' => isset($request->status) ? 1 : 0]);
        $bot_timing->status = $request->status;

        $bot_timing->save();

        $notify[] = ['success', 'New Bot Duration Added'];
        return back()->withNotify($notify);
    }
    public function update(Request $request)
    {
        $request->validate([
    		'title' => 'required|max:80',
    		'desc' => 'required|max:500',
    		'perc' => 'required|numeric',
    		'fake' => 'required|numeric',
    		'profit_missed' => 'numeric',
    		'min_profit' => 'required|numeric',
    		'max_profit' => 'required|numeric',
    		'image' => 'mimes:jpeg,jpg,png,svg'
    	]);


        $bot = Bot::findOrFail($request->id);
        $bot->title = $request->title;
        $bot->desc = $request->desc;
        $bot->perc = $request->perc;
        $bot->fake = $request->fake;
        $bot->profit_missed = $request->profit_missed;
        $bot->result_missed = $request->result_missed;
        $bot->min_profit = $request->min_profit;
        $bot->max_profit = $request->max_profit;
        $bot->limits = json_encode([
            'min_bot_amount' => $request->min_bot_amount,
            'max_bot_amount' => $request->max_bot_amount,
        ]);

        $request->merge(['is_new' => isset($request->is_new) ? 1 : 0]);
        $request->merge(['status' => isset($request->status) ? 1 : 0]);
        $bot->status = $request->status;
        $bot->is_new = $request->is_new;

        $path = imagePath()['bot']['path'];
        $size = imagePath()['bot']['size'];
        if ($request->hasFile('image')) {
            try {
                $filename = uploadImage($request->image, $path, $size);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
            $bot->image = $filename;
        }

        $bot->save();

        $notify[] = ['success', 'Bot has been Updated'];
        return redirect()->route('admin.bot.index')->withNotify($notify);
    }
    public function updateTime(Request $request)
    {
        $request->validate([
    		'duration' => 'required|numeric'
    	]);
        $bot_timing = BotTiming::findOrFail($request->id);
        $bot_timing->duration = $request->duration;
        $request->merge(['status' => isset($request->status) ? 1 : 0]);
        $bot_timing->status = $request->status;
        $bot_timing->save();

        $notify[] = ['success', 'Bot Duration has been Updated'];
        return back()->withNotify($notify);
    }
    public function remove(Request $request)
    {
        $request->validate(['id' => 'required|numeric']);
        $bot = Bot::findOrFail($request->id);
        $bot->delete();

        $notify[] = ['success', 'Bot has been removed'];
        return back()->withNotify($notify);
    }

    public function removeTime(Request $request)
    {
        $request->validate(['id' => 'required|numeric']);
        $bot_timing = BotTiming::findOrFail($request->id);
        $bot_timing->delete();

        $notify[] = ['success', 'Bot Duration has been removed'];
        return back()->withNotify($notify);
    }

    public function log()
    {
    	$page_title = "All Bot Contracts List";
    	$empty_message = "No Data Found";
        $user = User::get();
    	$bot_logs = BotContract::latest()->paginate(getPaginate());
    	return view('admin.bot.log', compact('page_title', 'empty_message', 'bot_logs','user'));
    }

    public function pending()
    {
        $page_title = "Pending Bot Contracts List";
        $empty_message = "No Data Found";
        $user = User::get();
        $bot_logs = BotContract::where('status', 0)->latest()->paginate(getPaginate());
        return view('admin.bot.log', compact('page_title', 'empty_message', 'bot_logs','user'));
    }

    public function completed()
    {
        $page_title = "Completed Bot Contracts List";
        $empty_message = "No Data Found";
        $user = User::get();
        $bot_logs = BotContract::where('status', 1)->latest()->paginate(getPaginate());
        return view('admin.bot.log', compact('page_title', 'empty_message', 'bot_logs','user'));
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $page_title = '';
        $user = User::get();
        $empty_message = 'No search result was found.';
        $bot_logs =  BotContract::whereHas('user',function($q) use ($search){
            $q->where('username', $search);
        });
        if($scope == 'pending') {
            $page_title .= 'Pending Bot Contracts Search';
            $bot_logs = $bot_logs->where('status', 0);
        }
        elseif($scope == 'completed') {
            $page_title .= 'Completed Bot Contracts Search';
            $bot_logs = $bot_logs->where('status', 1);
        }
        elseif($scope == 'list') {
            $page_title .= 'All Bot Contracts Search';
        }
        $bot_logs = $bot_logs->paginate(getPaginate());
        $page_title .= ' - ' . $search;
        return view('admin.bot.log', compact('page_title', 'empty_message', 'bot_logs', 'search','user'));
    }
    public function botResult(Request $request)
    {
        $bot_contracts = BotContract::where('id', $request->botId)->where('end_date', '<', Carbon::now())->get();
        $gnl = GeneralSetting::first();
        $gnl->last_cron_run =  Carbon::now();
        $gnl->save();

        foreach($bot_contracts as $bot_contract)
        {
			$wallet = Wallet::where('user_id',$bot_contract->user_id)->where('symbol',$bot_contract->pair)->first();
            
            if($bot_contract->result == 4) {
                // if(true){
                $wallet->balance += $bot_contract->amount + $bot_contract->profit;
                $wallet->save();
                $bot_contract->result = 1;
                $bot_contract->status = 1;
                $bot_contract->save();
            }
            
            else if($bot_contract->result == 5) {
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
        return response()->json(['success' => true, 'message' => 'Bot results executed.' . sizeof($bot_contracts)]);
    }

}
