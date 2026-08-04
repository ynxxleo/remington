<?php

use App\Http\Controllers\Admin\ManageContractsController;
use App\Http\Controllers\Admin\NetworkController;
use Illuminate\Support\Facades\Route;

// Networks
Route::group(['prefix' => 'networks', 'as' => 'networks.'], function() {
    Route::get('/', [NetworkController::class, 'index'])->name('index');
    Route::get('new',[NetworkController::class, 'new'])->middleware('demo')->name('new');
    Route::get('edit/{id}', [NetworkController::class, 'edit'])->middleware('demo')->name('edit');
    Route::post('store', [NetworkController::class, 'store'])->name('store')->middleware('demo');
    Route::post('update/{id}', [NetworkController::class, 'update'])->name('update')->middleware('demo');
    Route::post('remove', [NetworkController::class, 'remove'])->name('remove')->middleware('demo');
});

// Contracts
Route::group(['prefix' => 'contracts', 'as' => 'contracts.'], function() {
    Route::get('/', [ManageContractsController::class, 'index'])->name('index');
    Route::get('new',[ManageContractsController::class, 'new'])->middleware('demo')->name('new');
    Route::get('edit/{id}', [ManageContractsController::class, 'edit'])->middleware('demo')->name('edit');
    Route::post('store', [ManageContractsController::class, 'store'])->name('store')->middleware('demo');
    Route::post('update/{id}', [ManageContractsController::class, 'update'])->name('update')->middleware('demo');
    Route::post('remove', [ManageContractsController::class, 'remove'])->name('remove')->middleware('demo');
});

Route::group(['prefix' => 'ico', 'as' => 'ico.'], function() {
    Route::get('/', 'ManageIcoController@index')->name('index');
    Route::get('new', 'ManageIcoController@new')->middleware('demo')->name('new');
    Route::get('edit/{id}', 'ManageIcoController@edit')->middleware('demo')->name('edit');
    Route::post('store', 'ManageIcoController@store')->name('store')->middleware('demo');
    Route::post('update', 'ManageIcoController@update')->name('update')->middleware('demo');
    Route::post('remove', 'ManageIcoController@remove')->name('remove')->middleware('demo');

    // Log
    Route::get('log', 'ManageIcoController@log')->name('log.list');
    Route::get('log/pay/{id}', 'ManageIcoController@pay')->name('pay');
    Route::post('log/pay/metamask', 'ManageIcoController@create')->name('pay.metamask');
    Route::post('log/verify', 'ManageIcoController@verify')->name('verify');
    Route::get('pending/log', 'ManageIcoController@pending')->name('log.pending');
    Route::get('completed/log', 'ManageIcoController@completed')->name('log.completed');
    Route::get('{scope}/search', 'ManageIcoController@search')->name('log.search');
});
