<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountVerificationRequestModel;
use App\Http\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AccountVerificationController extends Controller
{
    use UserTrait;
    public function verifyEmail(Request $request)
    {
        $userEmail = $request->email;
        $userToken = $request->token;
        $accountVerificationRequest = AccountVerificationRequestModel::where('email', $userEmail)
            ->where('token', $userToken)
            ->first();
        if ($accountVerificationRequest == null)  return new JsonResponse(['message' => 'Invalid Token or Email!'], 403);
        if ($accountVerificationRequest->status == $this->statusComplete)  return new JsonResponse(['message' => 'Account is Already Verified!'], 200);

        try {
            DB::beginTransaction();
            $accountVerificationRequest->user()
                ->update(['email_verified_at' => now()]);

            $accountVerificationRequest->status = $this->statusComplete;
            $accountVerificationRequest->save();
            DB::commit();
            return new JsonResponse(['message' => 'Account has been verified'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return new JsonResponse(['message' => 'Something went wrong'], 500);
        }
    }
}
