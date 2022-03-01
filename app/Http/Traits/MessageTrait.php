<?php

namespace App\Http\Traits;

trait MessageTrait
{
    //auth error codes
    private $authUnverifiedUserErrorCode = 1;
    private $authBasicErrorCode = 2;
    //auth success code
    private $authSuccessCode = 3;

    //verify email client url
    private $verifyEmailClientUrl = '/emailVerification';
    private $resetPasswordClientUrl = '/resetPassword';
    //all error msg during auth
    private $unverifiedUserErrorResponse = 'Please Verify Your Email to login';

    //forget password link
    private $sendForgetPasswordLinkSuccessMessage = 'We have sent you a password reset link. Please check your email.';
    private $sendForgetPasswordLinkFailedMessage = 'Email not found in database or something went wrong';

    //password reset
    private $passwordResetSuccessMessage = 'Password has been updated';
    private $passwordResetFailedMessage = 'Email not found in database or something went wrong';
}
