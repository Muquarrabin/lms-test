<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Models\PasswordResetRequestModel;
use App\Http\Traits\UserTrait;
use App\Http\Traits\MessageTrait;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class ResetPasswordController extends Controller
{
    use UserTrait, MessageTrait;

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.

        if ($this->checkResetPasswordRequestExists($request->email, $request->token)) {
            $this->resetPassword($request->email, $request->password, $request->token);
            return $this->sendResetResponse($request, $this->passwordResetSuccessMessage);
        } else {
            return $this->sendResetFailedResponse($request, $this->passwordResetFailedMessage);
        }
    }

    public function checkResetPasswordRequestExists($email, $token)
    {
        return PasswordResetRequestModel::where('email', $email)
            ->where('token', $token)
            ->where('status', $this->statusPending)
            ->exists();
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    protected function validationErrorMessages()
    {
        return [];
    }


    protected function resetPassword($email, $password, $token)
    {

        try {
            DB::beginTransaction();
            $user = User::where('email', $email)->first();
            $user->password = Hash::make($password);
            $user->save();
            $passwordRequest = PasswordResetRequestModel::where('email', $email)
                ->where('token', $token)->first();
            $passwordRequest->status = $this->statusComplete;
            $passwordRequest->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }



    protected function sendResetResponse(Request $request, $response)
    {
        return  new JsonResponse(['message' => $response], 200);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        throw ValidationException::withMessages([
            'message' => $response,
        ]);
    }


    protected function guard()
    {
        return Auth::guard();
    }
}
