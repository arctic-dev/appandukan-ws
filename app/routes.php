<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});
Route::controller('user','UserController');
Route::controller('pancardoffline','PanoffilineController');
Route::controller('recharge','RechargeController');
Route::controller('itr','ItrController');
Route::controller('service','ServiceController');
Route::controller('ledgerreport','LedgerController');
Route::controller('ledger','LedgerreportController');
Route::Controller('icash','IcashController');