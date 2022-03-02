
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AccountVerificationController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('logout',   [AuthController::class, 'logout']);
});
Route::post('check-token', [AuthController::class, 'checkToken']);


//email verifaction routes
Route::post('/account/email-verified', [AccountVerificationController::class, 'verifyEmail']);

//forget passwrod request
Route::post('/forget-password', [ForgetPasswordController::class, 'sendResetLinkEmail']);

//password reset
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
