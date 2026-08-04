<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    //
    public function swap($locale){
        // available language in template array
        $availLocale=['en'=>'en', 'fr'=>'fr','de'=>'de','pt'=>'pt', 'vn'=>'vn', 'ar'=>'ar', 'th'=>'th'];
        // check for existing language
        if(array_key_exists($locale,$availLocale)){
            session()->put('locale',$locale);
        }
        return redirect()->back();
    }
}
