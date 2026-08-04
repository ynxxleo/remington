<?php

use App\Http\Controllers\MarketController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard/ico', 'IcoController@dash')->middleware('checkKYC')->name('home.ico');
Route::group(['middleware' => 'checkKYC', 'prefix' => 'ico', 'as' => 'ico.'], function() {
    Route::get('/', 'IcoController@index')->middleware('checkKYC')->name('index');
    Route::get('{symbol}/{pair}', 'IcoController@ico')->middleware('checkKYC')->name('now');
    Route::post('store', 'IcoController@store')->name('store');
    Route::post('wallet/create', 'IcoController@walletCreate')->name('wallet.create');
    Route::get('market', [MarketController::class, 'index'])->name('market');
});
