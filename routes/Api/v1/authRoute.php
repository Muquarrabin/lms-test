
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);


//email verifaction routes
Route::post('/account/email-verified', [AccountVerificationController::class, 'verifyEmail']);

//forget passwrod request
Route::post('/forget-password', [ForgetPasswordController::class, 'sendResetLinkEmail']);

//password reset
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
