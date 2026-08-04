<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Platform;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletsTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class WalletController extends Controller
{
    public function index()
    {
        $page_title = 'Wallets';
        $user = Auth::user();
        if(Wallet::where('user_id', $user->id)->exists()){
            $wallets = Wallet::where('user_id', $user->id)->get();
        } else {
            $wallets = "null";
        }
        return view('user.wallet.index', compact('page_title','wallets'));
    }

    public function wallet($address)
    {
        $page_title = 'Wallets';
        $user = Auth::user();
        if(Wallet::where('user_id', $user->id)->exists()){
            $wallets = Wallet::where('user_id', $user->id)->get();
        }
        $wal = Wallet::where('address', $address)->first();
        $wal_trx = WalletsTransactions::where('address', $address)->orWhere('to', $address)->latest()->paginate(getPaginate());
        session()->put('Track', $wal);
        $plat = Platform::first();
        return view('user.wallet.index', compact('page_title','wal','wallets','wal_trx','plat'));
    }
    public function trx($trx)
    {
        $page_title = 'Wallets';
        $wal = WalletsTransactions::where('trx',$trx)->first();
        $wallet = Wallet::where('address',$wal->address)->first();
        $wal_to = Wallet::where('address',$wal->to)->first();
        $user = User::where('id',$wal->user_id)->first();
        $fee = GeneralSetting::first()->exchange_fee / 100;
        return view('user.transactions.info', compact('page_title','trx','wal','user','wallet','wal_to','fee'));
    }

    public function invoice_print($trx)
    {
        $page_title = 'Print';
        $wal=WalletsTransactions::where('trx',$trx)->first();
        $wallet = Wallet::where('address',$wal->address)->first();
        $wal_to = Wallet::where('address',$wal->to)->first();
        $user = User::where('id',$wal->user_id)->first();
        $fee = GeneralSetting::first()->exchange_fee / 100;
        return view('user.transactions.print', compact('page_title','trx','wal','user','wallet','wal_to','fee'));
    }

    public function create(Request $request) {
        $user = Auth::user();
        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->address = grs(34);
        $wallet->symbol = $request->id;
        $wallet->save();
        $notify[] = ['success', 'Your ' . $wallet->symbol .' Wallet Created Successfully'];
        return back()->withNotify($notify);
    }

    public function send(Request $request)
    {
        $fee = GeneralSetting::first()->trx_fee / 100;
        if(Wallet::where('address',$request->from)->exists()){
            $sender_wallet = Wallet::where('address',$request->from)->first();
            $sender = User::where('id', $sender_wallet->user_id)->first();
            if(Wallet::where('address',$request->to)->exists()){
                $reciever_wallet = Wallet::where('address',$request->to)->first();
                if($sender_wallet->balance > $request->amount){
                    $wallet_new_trx = new WalletsTransactions();
                    $wallet_new_trx->user_id = $sender->id;
                    $wallet_new_trx->address = $sender_wallet->address;
                    $wallet_new_trx->amount = $request->amount;
                    $wallet_new_trx->amount_recieved = (getCoinRate($sender_wallet->symbol) / getCoinRate($reciever_wallet->symbol)) * $request->amount;
                    $wallet_new_trx->charge = $request->amount * $fee;
                    $wallet_new_trx->to = $reciever_wallet->address;
                    $wallet_new_trx->type = '3';
                    $wallet_new_trx->status = '1';
                    $wallet_new_trx->trx = grs(16);
                    $wallet_new_trx->details = $request->details;
                    $wallet_new_trx->save();

                    $sender_wallet->balance -= $request->amount + ($request->amount * $fee);
                    $sender_wallet->save();

                    $reciever_wallet->balance += (getCoinRate($sender_wallet->symbol) / getCoinRate($reciever_wallet->symbol)) * $request->amount;
                    $reciever_wallet->save();

                    $notify[] = ['success', 'Wallat Transaction Excuted Successfully'];
                    return back()->withNotify($notify);
                } else {
                    $notify[] = ['warning', 'You Dont Have Enought Balance!'];
                    return back()->withNotify($notify);
                }
            } else {
                $notify[] = ['warning', 'Reciever Wallet Not Found'];
                return back()->withNotify($notify);
            }
        } else {
            $notify[] = ['warning', 'Transaction Failed'];
            return back()->withNotify($notify);
        }
    }

    public function request(Request $request)
    {
        $fee = GeneralSetting::first()->trx_fee / 100;
        if(Wallet::where('address',$request->from)->exists()){
            $sender_wallet = Wallet::where('address',$request->from)->first();
            $sender = User::where('id', $sender_wallet->user_id)->first();
            if(Wallet::where('address',$request->to)->exists()){
                $reciever_wallet = Wallet::where('address',$request->to)->first();
                    $wallet_new_trx = new WalletsTransactions();
                    $wallet_new_trx->user_id = $sender->id;
                    $wallet_new_trx->address = $sender_wallet->address;
                    $wallet_new_trx->amount = $request->amount;
                    $wallet_new_trx->to = $reciever_wallet->address;
                    $wallet_new_trx->type = '4';
                    $wallet_new_trx->status = '2';
                    $wallet_new_trx->trx = grs(16);
                    $wallet_new_trx->details = $request->details;
                    $wallet_new_trx->save();

                    $notify[] = ['success', 'Request Sent Successfully'];
                    return back()->withNotify($notify);
            } else {
                $notify[] = ['warning', 'Reciever Wallet Not Found'];
                return back()->withNotify($notify);
            }
        } else {
            $notify[] = ['warning', 'Transaction Failed'];
            return back()->withNotify($notify);
        }
    }
    public function accept($trx)
    {
        $fee = GeneralSetting::first()->trx_fee / 100;
        $wal_trx = WalletsTransactions::where('trx',$trx)->firstOrFail();
        $wal_sender = Wallet::where('address',$wal_trx->to)->firstOrFail();
        $wal_requester = Wallet::where('address',$wal_trx->address)->firstOrFail();

        if (($wal_trx->amount + ($wal_trx->amount * $fee)) < $wal_sender->balance) {
            $wal_trx->status = 1;
            $wal_trx->save();
            if($wal_trx->save()){
                $wal_trx->status = '1';
                $wal_trx->amount_recieved = $wal_trx->amount;
                $wal_trx->charge = $wal_trx->amount * $fee;
                $wal_trx->details = 'Request Is Approved';
                $wal_trx->save();
                if($wal_trx->save()){
                    $wal_sender->balance -= ($wal_trx->amount + ($wal_trx->amount * $fee)) / getCoinRate($wal_sender->symbol);
                    $wal_sender->save();
                    if($wal_sender->save()){
                        $wal_requester->balance += $wal_trx->amount;
                        $wal_requester->save();
                        $notify[] = ['success', 'Request Approved Successfully'];
                    }
                }
            }
        } else {
            $notify[] = ['warning', 'You Dont Have Adequate Balance'];
        }
        return back()->withNotify($notify);
    }
    public function reject($trx)
    {
        $wal_trx = WalletsTransactions::where('trx',$trx)->firstOrFail();
        $wal_trx->status = 3;
        $wal_trx->save();

        $notify[] = ['success', 'Request Rejected Successfully'];
        return back()->withNotify($notify);

    }

    public function admincreateWallet(Request $request) {
        $wallet = new Wallet();
        $wallet->user_id = $request->user_id;
        $wallet->address = grs(34);
        $wallet->symbol = $request->symbol;
        $wallet->save();
        $notify[] = ['success', 'User ' . $wallet->symbol .' Wallet Created Successfully'];
        return back()->withNotify($notify);
    }
}
