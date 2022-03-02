<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Models\PasswordResetRequestModel;
use App\Http\Traits\UserTrait;
use App\Jobs\ForgetPasswordEmailJob;
use App\Http\Traits\MessageTrait;

class ForgetPasswordController extends Controller
{
    use UserTrait, MessageTrait;
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.

        if ($this->checkUserEmailExists($request->email)) {
            //generate token and save in db
            $token = $this->generatePasswordResetToken(128);
            $user = User::where('email', $request->email)->first();
            $this->createPasswordResetRequest($user, $token);
            //send email
            $url = $this->generatePasswordResetUrl($token, $request->email);
            $this->dispatch(new ForgetPasswordEmailJob($user->id, $user->name, $user->email, $token, $url));
            return $this->sendResetLinkResponse($request, $this->sendForgetPasswordLinkSuccessMessage);
        } else {
            //return error response of not exist
            return $this->sendResetLinkFailedResponse($request, $this->sendForgetPasswordLinkFailedMessage);
        }
    }
    public function generatePasswordResetUrl($token, $email)
    {
        $host = config('app.url') . $this->resetPasswordClientUrl;
        return $host . '?token=' . $token . '&email=' . $email;
    }

    public function generatePasswordResetToken(int $length)
    {
        return Str::random($length);
    }


    public function createPasswordResetRequest(User $user, $token)
    {
        $passwordResetData = [
            'user_id' => $user->id,
            'email'   => $user->email,
            'token'   => $token,
            'status'  => $this->statusPending
        ];
        PasswordResetRequestModel::create($passwordResetData);
    }

    public function checkUserEmailExists($email)
    {
        return User::where('email', $email)->exists();
    }

    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }


    protected function sendResetLinkResponse(Request $request, $response)
    {
        return  new JsonResponse(['message' => $response], 200);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        throw ValidationException::withMessages([
            'message' => $response,
        ]);
    }
}
