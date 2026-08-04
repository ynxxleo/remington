<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\MLM;
use App\Models\User;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function index()
    {
        $page_title = 'Database Optimizer';
        $bot = Extension::where('id',1)->first();
        $mlm = Extension::where('id',3)->first();
        return view('admin.setting.database', compact('page_title','bot','mlm'));
    }

    public function regenerate()
    {
        $users = User::get();
        foreach($users as $user){
            if(!MLM::where('username',$user->username)->exists()){
                $mlm = new MLM();
                $mlm->username = $user->username;
                $mlm->save();
            }
        }
        $notify[] = ['success', 'All Non Existing Users Rows Regenerated'];
        return back()->withNotify($notify);
    }
}
