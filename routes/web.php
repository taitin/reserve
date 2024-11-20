<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\WashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/schedule', function () {
    //執行 artisan command
    Artisan::call('wash:timeout_check');
    Artisan::call('wash:reply_check');
});


Route::get('/', [IndexController::class, 'index']);

Route::any('line/fake/{type?}/{message?}', [LineController::class, 'sandBox']);
Route::any('line/message', [LineController::class, 'lineCallback']);

Route::any('line/bind', [LineController::class, 'bind']);

Route::get('test', [WashController::class, 'test']);

Route::get('wash', [WashController::class, 'index'])->name('wash.index');

Route::get('wash/portal', [WashController::class, 'portal']);;

Route::post('wash/set_member', [WashController::class, 'setMember']);

Route::get('wash/check_member/{social_id}', [WashController::class, 'checkMember'])->name('wash.show');
Route::get('wash/{id}/re_book',  [WashController::class, 'index']);

Route::get('wash/get_profile/{social_id}', [WashController::class, 'getProfile'])->name('wash.index');
Route::get('wash/get_available_time', [WashController::class, 'getAvailableTime']);

Route::get('wash/get_projects',  [WashController::class, 'getProjects']);
Route::get('wash/get_additions',  [WashController::class, 'getAdditions']);

Route::post('wash',  [WashController::class, 'store'])->name('wash.store');


Route::get('wash/{id}/pay',  [WashController::class, 'pay']);
Route::post('wash/pay',  [WashController::class, 'paid']);
Route::post('wash/set_return',  [WashController::class, 'setReturn']);


Route::get('wash/pay_fake',  [WashController::class, 'payFake']);

Route::get('wash/{id}/arrange',  [WashController::class, 'arrange']);
Route::get('wash/{id}/set_amount',  [WashController::class, 'setAmount']);
Route::post('wash/{id}/set_amount',  [WashController::class, 'doSetAmount']);
Route::get('wash/{id}/redirect_pay',  [WashController::class, 'redirectPay']);
Route::get('wash/{id}/pay_webhook/{token}',  [WashController::class, 'payWebhook']);
Route::any('wash/autopass/callback',  [WashController::class, 'callBack']);


Route::get('wash/{id}/pay_trigger',  [WashController::class, 'payWebhookFake']);
Route::get('wash/{id}/time_adjust',  [WashController::class, 'adjustTime']);
Route::post('wash/{id}/time_adjust',  [WashController::class, 'saveAdjustTime']);







Route::post('wash/arrange',  [WashController::class, 'arranged']);

Route::get('wash/{id}/before',  [WashController::class, 'before']);
Route::get('wash/{id}/after',  [WashController::class, 'after']);
