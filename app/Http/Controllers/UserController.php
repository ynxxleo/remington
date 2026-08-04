<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\Commission;
use App\Models\User;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use App\Models\TradeLog;
use App\Models\KYC;
use App\Models\PracticeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\EmailToUser;
use App\Models\Platform;
use App\Models\Wallet;
use App\Models\WalletsTransactions;

use App\Notifications\Withdraw;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
    }
    
    // private $globAPI = "https://nft-mini-api.herokuapp.com/api/v1/";
    private $globAPI = "https://spotalert-api.herokuapp.com/api/v1/";
    
    private function callApiPost($url, $fields) {
        $ch = curl_init($url);
        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array("response" => $data, "code" => $httpcode);
    }

    private function callApiGET($url)
    {
        $ch = curl_init($url);
        $options = [
            CURLOPT_POST => false,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array("response" => $data, "code" => $httpcode);
    }
    
    public function market()
    {
        $page_title = 'Dashboard';
        $user = Auth::user();
        return view('user.market', compact('page_title', 'user'));
    }
    
    public function marketr()
    {
        $plat = Platform::where('id',1)->first();
        if($plat->binary == 1) {
            return redirect()->route('user.practice.market');
        } else {
            return redirect()->route('user.exchange.market');
        }
    }
    
    public function copytrader(Request $request)
    {
        $user = Auth::user();
        if($user->role_id == 1) {
            return redirect()->route('user.masterusers');
        }
        else {
            return redirect()->route('user.listmasterusers');
        }
    }
    
    public function masterusers(Request $request)
    {
        $page_title = 'Master Users';
        $empty_message = 'No User Applying for Master Trade';
        
        $referrals = User::where(function ($query) {
            $query->where('isMaster', '=', 1)->orWhere('isMaster', '=', 2);
        })->latest()->paginate(getPaginate());
        
        // $referrals = User::where('isMaster', 2)->latest()->paginate(getPaginate());
        
        return view('user.masters', compact('page_title', 'empty_message', 'referrals'));
    }
    
    public function becomemaster(Request $request)
    {
        $user = Auth::user();
        $user->isMaster = 1;
        $user->save();
        return "Hello Master";
    }
    
    public function copymaster(Request $request)
    {
        
        return 'make peace';
        
        $mastersInfo = User::where('email', $request->email)->first();
        $user = Auth::user();
        
        $url = $this->globAPI . "oth/copy-trade";
        
        $fields = array(
            'copier' => $user->id . '',
            'master' => $mastersInfo->id . ''
        );
        
        $result = $this->callApiPost($url, $fields);
        $responseCode = $result["code"];
        $result = $result["response"];
        $result = json_decode($result, true);
        
        return array(
            'code' => $responseCode,
            'message' => $result["message"]
        );
    }
    
    public function becomemasterfinal(Request $request)
    {
        $user = User::where('email', $request->email)->update([
            'isMaster' => 2,
            'copy_gain' => $request->copy_gain,
            'copy_copier1' => $request->copy_copier1,
            'copy_copier2' => $request->copy_copier2,
            'copy_profit' => $request->copy_profit,
            'copy_loss' => $request->copy_loss,
            'copy_floating_profit' => $request->copy_floating_profit,
            'copy_equity' => $request->copy_equity,
            'copy_master_trader_bonus' => $request->copy_master_trader_bonus,
            'copy_leverage' => $request->copy_leverage,
            'copy_fee' => $request->copy_fee,
            'copy_min_amt' => $request->copy_min_amt,
        ]);
        return "Completed";
    }
    
    public function becomemasterfinally(Request $request)
    {
        $user = User::where('email', $request->email)->update([
            'copy_gain' => $request->copy_gain,
            'copy_copier1' => $request->copy_copier1,
            'copy_copier2' => $request->copy_copier2,
            'copy_profit' => $request->copy_profit,
            'copy_loss' => $request->copy_loss,
            'copy_floating_profit' => $request->copy_floating_profit,
            'copy_equity' => $request->copy_equity,
            'copy_master_trader_bonus' => $request->copy_master_trader_bonus,
            'copy_leverage' => $request->copy_leverage,
            'copy_fee' => $request->copy_fee,
            'copy_min_amt' => $request->copy_min_amt,
        ]);
        return "Completed";
    }
    
    public function copycheck(Request $request)
    {
        $response = "";
        $fee = $request->fee;
        
        $userInfo = User::where('id', $request->user)->first();
        $masterInfo = User::where('id', $request->master)->first();
        
        $userBalance = $userInfo->balance;
        $masterBalance = $masterInfo->balance;
        
        $userUsdtWallet = Wallet::where('user_id', $request->user)->where('symbol', 'USDT')->first();
        $masterUsdtWallet = Wallet::where('user_id', $request->master)->where('symbol', 'USDT')->first();
        
        $userBalanceUSDT = ( ( $userUsdtWallet == null ) ? 0 : $userUsdtWallet->balance );
        $masterBalanceUSDT = ( ( $masterUsdtWallet == null ) ? 0 : $masterUsdtWallet->balance );
        
        $masterMinAmt = $masterInfo->copy_min_amt;
        $masterCopyFee = $masterInfo->copy_fee;
        
        $nfee = (int) $fee;
        // $nfee = number_format($nfee, 2);
        
        $nMasterMinAmt = (int) $masterMinAmt;
        // $nMasterMinAmt = number_format($nMasterMinAmt, 2);
        
        $nMasterCopyFee = (int) $masterCopyFee;
        // $nMasterCopyFee = number_format($nMasterCopyFee, 2);
        
        $nUserBalanceUSDT = (int) $userBalanceUSDT;
        // $nUserBalanceUSDT = number_format($nUserBalanceUSDT, 2);
        
        $nMasterBalanceUSDT = (int) $masterBalanceUSDT;
        // $nMasterBalanceUSDT = number_format($nMasterBalanceUSDT, 2);
        
        if ($nMasterCopyFee == "") {
            return "This User has no Copy Fee";
        }
        
        if ($nUserBalanceUSDT < $nMasterCopyFee) {
            return "Cannot have Below Copy Fee, Please topup your balance to continue";
        }
        
        if ($nUserBalanceUSDT < $nMasterMinAmt) {
            return "Cannot have Below Minimum Fee, Please top up your balance to continue";
        }
        
        $debitBalance = $nUserBalanceUSDT - $nMasterCopyFee;
        $creditBalance = $nMasterBalanceUSDT + $nMasterCopyFee;
        
        // $userBal = User::where('id', $request->user)->update([
        //     'balance' => $debitBalance,
        // ]);
        
        // $masterBal = User::where('id', $request->master)->update([
        //     'balance' => $creditBalance,
        // ]);
        
        $userBal = Wallet::where('user_id', $request->user)->where('symbol', 'USDT')->update([
            'balance' => $debitBalance,
        ]);
        
        $masterBal = Wallet::where('user_id', $request->master)->where('symbol', 'USDT')->update([
            'balance' => $creditBalance,
        ]);
        
        $response = "completed";
        
        return $response;
    }
    
    public function sendmychat(Request $request)
    {
        $mastersInfo = User::where('email', $request->email)->first();
        $user = Auth::user();
        
        $userId = $user->id . '';
        $masterId = $mastersInfo->id . '';
        
        $fields = array(
            'from_user' => $user->id . '',
            'to_user' => $mastersInfo->id . '',
            'chat_message' => $request->chat_msg,
            'chat_status' => false
        );
        
        $url = $this->globAPI . "oth/send-chat-message";
        $result = $this->callApiPost($url, $fields);
        
        $responseCode = $result["code"];
        $result = $result["response"];
        $result = json_decode($result, true);
        
        return $result;
    }
    
    public function mychatusers(Request $request)
    {
        $mastersInfo = User::where('email', $request->email)->first();
        $user = Auth::user();
        
        $userId = $user->id . '';
        $masterId = $mastersInfo->id . '';
        
        $url = $this->globAPI . "oth/get-chat-message/".$userId . "/" . $masterId;
        $result = $this->callApiGET($url);
        
        $responseCode = $result["code"];
        $result = $result["response"];
        $result = json_decode($result, true);
        $chatMessages = $result["myChats"];
        
        $listMessages = array();
        
        foreach($chatMessages as $key => $msg) {
            
            $fromUser = $msg["from_user"];
            $toUser = $msg["to_user"];
            
            $fromUserInfo = User::where('id', $fromUser)->first();
            $toUserInfo = User::where('id', $toUser)->first();
            
            $msgArr = array(
                'from_user' => $fromUserInfo->lastname . ' ' . $fromUserInfo->firstname,
                'to_user' => $toUserInfo->lastname . ' ' . $toUserInfo->firstname,
                'msg_id' => $msg["_id"],
                'time_sent' => $msg["createdAt"],
                'msg_status' => $msg["status"],
                'message' => $msg["message"]
            );
            
            array_push($listMessages, $msgArr);
            
        }
        
        return $listMessages;
    }
    
    public function listmasterusers(Request $request)
    {
        $page_title = 'List Master Users';
        $empty_message = 'No Master Trade Users';
        
        $user = Auth::user();
        
        $userId = $user->id . '';
        $actualMainUserId = $userId;
        
        $url = $this->globAPI . "oth/master-traders/".$userId;
        $result = $this->callApiGET($url);
        $responseCode = $result["code"];
        $result = $result["response"];
        $result = json_decode($result, true);
        $copiedMasterUsers = $result["copiers"];
        
        // $referrals = User::where('isMaster', 2)->where('id', '!=', $user->id)->latest()->paginate(getPaginate());
        $referrals = User::where('isMaster', 2)->latest()->paginate(getPaginate());
        
        return view('user.listmasterusers', compact('page_title', 'empty_message', 'referrals', 'copiedMasterUsers', 'actualMainUserId'));
    }
    
    public function chat(Request $request)
    {
        $page_title = 'User Chat';
        
        $empty_message = 'No Users to chat with';
        
        $user = Auth::user();
        
        $userId = $user->id . '';
        
        $url = $this->globAPI . "oth/my-list/".$userId;
        $result = $this->callApiGET($url);
        $responseCode = $result["code"];
        $result = $result["response"];
        $result = json_decode($result, true);
        $copiedMasterUsers = $result["copiers"];
        
        $referrals = User::where('isMaster', 2)->latest()->paginate(getPaginate());
        return view('user.chat.index', compact('page_title', 'empty_message', 'referrals', 'copiedMasterUsers'));
    }
    
    public function home()
    {
        $plat = Platform::where('id',1)->first();
        if($plat->binary == 1) {
            return redirect()->route('user.home.practice');
        } else {
            return redirect()->route('user.home.exchange');
        }
    }
    public function home1()
    {
        $page_title = 'Dashboard';
        return view('user.dashboard1', compact('page_title'));
    }

    public function dash()
    {
        $page_title = 'Dashboard';
        $user = Auth::user();
        $user_kyc = KYC::where('userId', $user->id)->first();
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $deposit = auth()->user()->deposits()->sum('amount');
        $withdraw = Withdrawal::where('user_id', $user->id)->where('status', '!=', 0)->sum('amount');
        $transaction = auth()->user()->transactions()->count();
        $tradeWon = TradeLog::where('user_id', $user->id)->where('result', 1)->sum('amount');
        $tradeLog = TradeLog::where('user_id', $user->id)->count();
        $tradeWin = TradeLog::where('user_id', $user->id)->where('result', 1)->count();
        $tradeLose = TradeLog::where('user_id', $user->id)->where('result', 2)->count();
        $tradeDraw = TradeLog::where('user_id', $user->id)->where('result', 3)->count();
        $perc['tradeWon_last_week'] = TradeLog::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('result', 1)->sum('amount');
        if($tradeWin > 0) {
            $perc['tradeWon_last_week_percentage'] = $perc['tradeWon_last_week'] > 0 ? ceil((($perc['tradeWon_last_week']) * 100) / $tradeWin) : 0;
        } else {
            $perc['tradeWon_last_week_percentage'] = 0;
        }
        $tradelogs = TradeLog::where('user_id', $user->id)->latest()->limit(10)->get();
        $gnl = GeneralSetting::first();
        return view('user.dashboard', compact('page_title','user_kyc','gnl','perc', 'user','tradeWon' , 'deposit', 'withdraw', 'transaction', 'tradeLog', 'tradeWin', 'tradeLose', 'tradeDraw', 'tradelogs'));
    }
    
    public function practiceDash(Request $request)
    {
        $page_title = 'Dashboard';
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $user = Auth::user();
        $user_kyc = KYC::where('userId', $user->id)->first();
        $deposit = auth()->user()->deposits()->sum('amount');
        $withdraw = Withdrawal::where('user_id', $user->id)->where('status', '!=', 0)->sum('amount');
        $transaction = auth()->user()->transactions()->count();
        $tradeWon = PracticeLog::where('user_id', $user->id)->where('result', 1)->sum('amount');
        $tradeLog = PracticeLog::where('user_id', $user->id)->count();
        $tradeWin = PracticeLog::where('user_id', $user->id)->where('result', 1)->count();
        $tradeLose = PracticeLog::where('user_id', $user->id)->where('result', 2)->count();
        $tradeDraw = PracticeLog::where('user_id', $user->id)->where('result', 3)->count();
        $tradelogs = PracticeLog::where('user_id', $user->id)->latest()->limit(10)->get();
        $perc['tradeWon_last_week'] = PracticeLog::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('result', 1)->sum('amount');
        if($tradeWin > 0) {
            $perc['tradeWon_last_week_percentage'] = $perc['tradeWon_last_week'] > 0 ? ceil((($perc['tradeWon_last_week']) * 100) / $tradeWin) : 0;
        } else {
            $perc['tradeWon_last_week_percentage'] = 0;
        }
        $platform = Platform::where('id',1)->first();
        $gnl = GeneralSetting::first();
        return view('user.dashboard', compact('page_title','gnl', 'user','platform','perc','user_kyc', 'deposit','tradeWon' , 'tradeLog', 'tradeWin', 'tradeLose', 'tradeDraw', 'tradelogs', 'withdraw', 'transaction'));
    }

    public function send_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ], [
            'user_id.required' => __('Select a user first!'),
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('name')) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.somthing_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
        } else {
            $user = User::FindOrFail($request->input('user_id'));

            if ($user) {
                $msg = $request->input('message');
                $msg = replace_with($msg, '[[user_name]]', $user->name);
                $data = (object) [
                    'user' => (object) ['name' => $user->name, 'email' => $user->email],
                    'subject' => $request->input('subject'),
                    'greeting' => $request->input('greeting'),
                    'text' => str_replace("\n", "<br>", $msg),
                ];
                $when = now()->addMinutes(2);

                try {
                    Mail::to($user->email)
                    ->later($when, new EmailToUser($data));
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.mail.send');
                } catch (\Exception $e) {
                    $ret['errors'] = $e->getMessage();
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('messages.mail.issues');
                }
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.mail.failed');
            }

            if ($request->ajax()) {
                return response()->json($ret);
            }
            return back()->with([$ret['msg'] => $ret['message']]);
        }
    }

    public function addPracticeBalance()
    {
        $gnl = GeneralSetting::first();
        $user = Auth::user();
        $user->practice_balance = $gnl->practice_balance;
        $user->save();
        $notify[] = ['success','Practice Balance Add Successfully'];
        return back()->withNotify($notify);
    }

    /*
     * transaction History
     */
    public function transactionHistory()
    {
        $page_title = 'Transactions History';
        $empty_message = 'No transaction found.';
        $transactions = auth()->user()->transactions()->latest()->paginate(getPaginate());
        return view('user.transaction', compact('page_title', 'empty_message', 'transactions'));
    }


    public function commissions()
    {
        $user = Auth::user();
        $page_title = 'Commission History';
        $empty_message = 'No Commission found.';
        $commissions =Commission::where('user_id', $user->id)->latest()->paginate(getPaginate());
        return view('user.commissions', compact('page_title', 'empty_message', 'commissions'));
    }

    public function referralog()
    {
        $user = Auth::user();
        $page_title = 'Referral Log';
        $empty_message = 'No Referral User';
        $referrals = User::where('ref_by', $user->id)->latest()->paginate(getPaginate());
        return view('user.referral', compact('page_title', 'empty_message', 'referrals'));
    }

    /*
     * Deposit History
     */
    public function depositHistory()
    {
        $page_title = 'Deposit History';
        $empty_message = 'No history found.';
        $logs = auth()->user()->deposits()->with(['gateway'])->latest()->paginate(getPaginate());
        return view('user.deposit_history', compact('page_title', 'empty_message', 'logs'));
    }
    /*
     * Withdraw Operation
     */
    public function withdrawMoney($address)
    {
        $data['withdrawMethod'] = WithdrawMethod::whereStatus(1)->get();
        $data['page_title'] = "Withdraw Money";
        $data['address'] = $address;
        return view('user.withdraw.methods', $data);
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $user = auth()->user();
        $wallet = Wallet::where('user_id',$user->id)->where('address', $request->address)->first();
        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your Requested Amount is Smaller Than Minimum Amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your Requested Amount is Larger Than Maximum Amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $wallet->balance) {
            $notify[] = ['error', 'Your do not have Sufficient Balance For Withdraw.'];
            return back()->withNotify($notify);
        }

        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = getAmount($afterCharge * $method->rate);

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id;
        $withdraw->user_id = $user->id;
        $withdraw->amount = getAmount($request->amount * getCoinRate($wallet->symbol));
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->address = $request->address;
        $withdraw->symbol = $wallet->symbol;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();
        session()->put('wtrx', $withdraw->trx);
        return redirect()->route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $data['withdraw'] = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->latest()->firstOrFail();
        $data['page_title'] = "Withdraw Preview";
        $user = Auth::user();
        $data['wallet'] = Wallet::where('user_id',$user->id)->where('address',$data['withdraw']->address)->first();
        return view('user.withdraw.preview', $data);
    }


    public function withdrawSubmit(Request $request)
    {
        $general = GeneralSetting::first();
        $withdraw = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->latest()->firstOrFail();
        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $this->validate($request, $rules);
        $user = Auth::user();
        $wallet = Wallet::where('user_id',$user->id)->where('address',$withdraw->address)->first();

        if (getAmount($withdraw->amount) > $wallet->balance) {
            $notify[] = ['error', 'Your Request Amount is Larger Then Your Current Balance.'];
            return back()->withNotify($notify);
        }

        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['withdraw']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }

        $withdraw->status = 2;
        $withdraw->save();
        $wallet->balance  -=  $withdraw->amount;
        $wallet->save();

        if ($wallet->save()) {
            $transaction = new Transaction();
            $transaction->user_id = $withdraw->user_id;
            $transaction->amount = getAmount($withdraw->amount);
            $transaction->post_balance = getAmount($wallet->balance);
            $transaction->charge = getAmount($withdraw->charge);
            $transaction->trx_type = '-';
            $transaction->details = getAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
            $transaction->trx =  $withdraw->trx;
            $transaction->save();
            $transaction->user->notify((new Withdraw($transaction, $withdraw, 'user-requested')));

            if($transaction->save()){
                $wallet_new_trx = new WalletsTransactions();
                $wallet_new_trx->user_id = $withdraw->user_id;
                $wallet_new_trx->address = $withdraw->address;
                $wallet_new_trx->amount = $withdraw->amount;
                $wallet_new_trx->amount_recieved = $withdraw->amount / getCoinRate($withdraw->symbol);
                $wallet_new_trx->charge = getAmount($withdraw->charge);
                $wallet_new_trx->to = $withdraw->address;
                $wallet_new_trx->type = '2';
                $wallet_new_trx->status = '2';
                $wallet_new_trx->trx = $withdraw->trx;
                $wallet_new_trx->details = 'Withdraw of '.$withdraw->amount.' '.$withdraw->symbol.' From Wallet';
                $wallet_new_trx->save();
            }
        }

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from '.$user->username;
        $adminNotification->click_url = route('admin.withdraw.details',$withdraw->id);
        $adminNotification->save();

        $notify[] = ['success', 'Withdraw Request Successfully Send'];
        return redirect()->route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
    {
        $data['page_title'] = "Withdraw Log";
        $data['withdraws'] = Withdrawal::where('user_id', Auth::id())->where('status', '!=', 0)->with('method')->latest()->paginate(getPaginate());
        $data['empty_message'] = "No Data Found!";
        return view('user.withdraw.log', $data);
    }
}
