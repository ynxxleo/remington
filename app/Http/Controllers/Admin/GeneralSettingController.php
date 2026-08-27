<?php

namespace App\Http\Controllers\Admin;

use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Image;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $general = GeneralSetting::first();
        $limits = json_decode($general->limits);
        $page_title = 'Settings';
        $bot = Extension::where('id',1)->first();
        $cors = File::get(resource_path('assets/cors.txt'));
        return view('admin.setting.general_setting', compact('page_title', 'general','limits','bot','cors'));
    }

    public function update(Request $request)
    {
        $validation_rule = [
            'exchange_fee' => ['numeric'],
            'trx_fee' => ['numeric'],
            'referral_bonus' => ['numeric'],
            'profit' => ['numeric'],
            'practice_balance' => ['numeric'],
        ];

        $validator = Validator::make($request->all(), $validation_rule, []);
        $validator->validate();

        $general_setting = GeneralSetting::first();
        $settings = Setting::get();
        $sitename = $settings->where('field','site_name')->first();
        $sitename->value = $request->sitename;
        $sitename->save();
        /*$dash_route = $settings->where('field','dash_route')->first();
        $dash_route->value = $request->dash_route;
        $dash_route->save();*/

        $request->merge(['force_ssl' => isset($request->force_ssl) ? 1 : 0]);
        $request->merge(['registration' => isset($request->registration) ? 1 : 0]);
        $request->merge(['referal_status' => isset($request->referal_status) ? 1 : 0]);

        $general_setting->sitename = $request->sitename ;
        $general_setting->cur_text = $request->cur_text ;
        $general_setting->cur_sym = $request->cur_sym ;
        $general_setting->force_ssl = $request->force_ssl ;
        $general_setting->practice_balance = $request->practice_balance ;
        $general_setting->practice_wallet = $request->practice_wallet ;
        $general_setting->registration = $request->registration ;
        $general_setting->profit = $request->profit ;
        $general_setting->referral_bonus = $request->referral_bonus ;
        $general_setting->referal_status = $request->referal_status ;
        $general_setting->coin_api_key = $request->coin_api_key ;
        $general_setting->coin_rate_api = $request->coin_rate_api ;
        $general_setting->exchange_fee = $request->exchange_fee ;
        $general_setting->trx_fee = $request->trx_fee ;
        $general_setting->limits = json_encode([
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'min_time_sec' => $request->min_time_sec,
            'max_time_sec' => $request->max_time_sec,
            'min_time_min' => $request->min_time_min,
            'max_time_min' => $request->max_time_min,
            'min_time_hour' => $request->min_time_hour,
            'max_time_hour' => $request->max_time_hour,
        ]);

        file_put_contents(resource_path('assets/cors.txt'), $request->cors);
        $general_setting->save() ;
        $notify[] = ['success', 'General Setting has been updated.'];
        return back()->withNotify($notify);
    }


    public function logoIcon()
    {
        $page_title = 'Logo & Icon';
        return view('admin.setting.logo_icon', compact('page_title'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'favicon' => 'nullable|image|mimes:png|max:1024',
        ]);
        if ($request->hasFile('logo')) {
            try {
                $path = public_path(imagePath()['logoIcon']['path']);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make($request->file('logo'))
                    ->orientate()
                    ->resize(700, 150, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($path . '/logo.png', 92);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Logo could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                $path = public_path(imagePath()['logoIcon']['path']);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size = explode('x', imagePath()['favicon']['size']);
                Image::make($request->file('favicon'))->fit($size[0], $size[1])->save($path . '/favicon.png', 92);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Favicon could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Sitewide logo and favicon have been updated.'];
        return back()->withNotify($notify);
    }
}
