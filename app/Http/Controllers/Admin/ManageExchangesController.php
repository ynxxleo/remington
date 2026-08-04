<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeLogs;
use App\Models\User;
use Illuminate\Http\Request;

class ManageExchangesController extends Controller
{
    public function log()
    {
    	$page_title = "All Exchanges List";
    	$empty_message = "No Data Found";
        $user = User::get();
    	$exchanges = ExchangeLogs::latest()->paginate(getPaginate());
    	return view('admin.exchange.log', compact('page_title', 'empty_message', 'exchanges','user'));
    }

    public function pending()
    {
        $page_title = "Pending Exchanges List";
        $empty_message = "No Data Found";
        $user = User::get();
        $exchanges = ExchangeLogs::where('status', 0)->latest()->paginate(getPaginate());
        return view('admin.exchange.log', compact('page_title', 'empty_message', 'exchanges','user'));
    }

    public function completed()
    {
        $page_title = "Completed Exchanges List";
        $empty_message = "No Data Found";
        $user = User::get();
        $exchanges = ExchangeLogs::where('status', 1)->latest()->paginate(getPaginate());
        return view('admin.exchange.log', compact('page_title', 'empty_message', 'exchanges','user'));
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $page_title = '';
        $user = User::get();
        $empty_message = 'No search result was found.';
        $exchanges =  ExchangeLogs::whereHas('user',function($q) use ($search){
            $q->where('username', $search);
        });
        if($scope == 'pending') {
            $page_title .= 'Pending Exchanges Search';
            $exchanges = $exchanges->where('status', 0);
        }
        elseif($scope == 'completed') {
            $page_title .= 'Completed Exchanges Search';
            $exchanges = $exchanges->where('status', 1);
        }
        elseif($scope == 'list') {
            $page_title .= 'All Exchanges Search';
        }
        $exchanges = $exchanges->paginate(getPaginate());
        $page_title .= ' - ' . $search;
        return view('admin.exchange.log', compact('page_title', 'empty_message', 'exchanges', 'search','user'));
    }
}
