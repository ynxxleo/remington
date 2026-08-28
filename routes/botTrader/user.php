<?php

use App\Http\Controllers\MarketController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard/bot', 'BotController@dash')->middleware('checkKYC','checkBot')->name('home.bot');
Route::get('market-data/candles', 'BotController@candles')->middleware('checkKYC')->name('market.candles');
Route::group(['middleware' => 'checkKYC', 'prefix' => 'bot', 'as' => 'bot.'], function() {
    Route::get('/', 'BotController@index')->middleware('checkKYC','checkBot')->name('index');
    Route::get('{symbol}/{pair}', 'BotController@bot')->middleware('checkKYC','checkBot')->name('now');
    Route::post('store', 'BotController@store')->name('store');
    Route::get('market', [MarketController::class, 'index'])->name('market');
});
Route::get('dashboard/fx/bot', 'BotController@fxdash')->middleware('checkKYC','checkBot')->name('home.fxbot');
Route::group(['middleware' => 'checkKYC', 'prefix' => 'fxbot', 'as' => 'fxbot.'], function() {
    Route::get('/', 'BotController@fxindex')->middleware('checkKYC')->name('index.fx');
     Route::get('{symbol}/{pair}', 'BotController@fxbot')->middleware('checkKYC','checkBot')->name('fxnow');
});

Route::get('dashboard/st/bot', 'BotController@stdash')->middleware('checkKYC','checkBot')->name('home.stbot');
Route::group(['middleware' => 'checkKYC', 'prefix' => 'stbot', 'as' => 'stbot.'], function() {
    Route::get('/', 'BotController@stindex')->middleware('checkKYC')->name('index.stock');
    Route::post('buy/stock','BotController@buystock')->name('stock.buy');
    //  Route::get('{symbol}/{pair}', 'BotController@stbot')->middleware('checkKYC','checkBot')->name('stnow');
});
