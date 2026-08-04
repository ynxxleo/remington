<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index()
    {
        $page_title = 'Platform Settings';
        $platform = Platform::where('id',1)->first();
        $bot = Extension::where('id',1)->first();
        $ico = Extension::where('id',2)->first();
        $mlm = Extension::where('id',3)->first();
        return view('admin.setting.platform', compact('page_title','platform','bot','ico','mlm'));
    }

    public function update(Request $request)
    {
        $platform = Platform::where('id',1)->first();
        $request->merge(['binary' => isset($request->binary) ? 1 : 0]);
        $request->merge(['bottrader' => isset($request->bottrader) ? 1 : 0]);
        $request->merge(['bot_fake' => isset($request->bot_fake) ? 1 : 0]);
        $request->merge(['ico' => isset($request->ico) ? 1 : 0]);
        $request->merge(['kyc' => isset($request->kyc) ? 1 : 0]);
        $request->merge(['subdomain' => isset($request->subdomain) ? 1 : 0]);
        $request->merge(['mlm' => isset($request->mlm) ? 1 : 0]);
        $request->merge(['wallet_address' => isset($request->wallet_address) ? 1 : 0]);
        $platform->binary = $request->binary ;
        $platform->bot_fake = $request->bot_fake;
        $platform->kyc = $request->kyc ;
        $platform->subdomain = $request->subdomain ;
        $platform->wallet_address = $request->wallet_address ;
        $platform->save();

        $bot = Extension::where('id',1)->first();
        $ico = Extension::where('id',2)->first();
        $mlm = Extension::where('id',3)->first();
        $bot->status = $request->bottrader ;
        $ico->status = $request->ico ;
        $mlm->status = $request->mlm ;
        $bot->save() ;
        $ico->save() ;
        $mlm->save() ;

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $notify[] = ['success', 'Platform Setting has been updated.'];
        return back()->withNotify($notify);
    }
}
