<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Traits\UserTrait;
use App\Http\Traits\MessageTrait;
use App\Jobs\AccountVerificationEmailJob;
use App\Models\AccountVerificationRequestModel;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    //
    use AuthenticatesUsers, UserTrait, MessageTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'getAuthenticatedUser');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }


    /**
     * Validate the user Register request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);
    }


    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        $this->validateRegister($request);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $role = Role::findByName('student');
            $role->users()->attach($user);
            //generate token
            $token = $this->generateEmailVerificationToken(128);

            //insert verification data
            $this->generateEmailVerificationRequest($user, $token);

            $url = $this->generateAccountVerificationUrl($token, $user->email);
            //trigger account verification job
            $this->dispatch(new AccountVerificationEmailJob($user->id, $user->name, $user->email, $token, $url));


            DB::commit();

            return  new JsonResponse([
                'user_info' => $user,
                'message' => 'Your account has been created. Please verify your email to log in.',
                'success' => true
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return new JsonResponse(['errors' => $exception->getMessage(), 'success' => false], 500);
        }
    }

    public function generateAccountVerificationUrl($token, $email)
    {
        $host = config('app.url') . $this->verifyEmailClientUrl;
        return $host . '?token=' . $token . '&email=' . $email;
    }



    public function generateEmailVerificationToken(int $length)
    {
        return Str::random($length);
    }


    public function generateEmailVerificationRequest(User $user, $token)
    {
        $accountVerificationData = [
            'user_id' => $user->id,
            'email'   => $user->email,
            'token'   => $token,
            'status'  => $this->statusPending
        ];
        AccountVerificationRequestModel::create($accountVerificationData);
    }








    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    }



    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user == null) return $this->authBasicErrorCode;
        if ($user->email_verified_at == null) return $this->authUnverifiedUserErrorCode;
        $authStatus =  $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
        if ($authStatus == false) return $this->authBasicErrorCode;
        return $this->authSuccessCode;
    }




    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);


        return new JsonResponse($this->generateAuthenticatedUserData(), 200);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'message' => [trans('auth.failed')],
        ]);
    }


    protected function sendUnVerifiedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'message' => $this->unverifiedUserErrorResponse,
        ]);
    }


    private function generateAuthenticatedUserData()
    {

        return [
            'user' => $this->guard()->user(),
            'authCheck' => Auth::check(),
        ];
    }


    public function checkToken(Request $request)
    {
        $token = $request->token;
        $personalAccessToken =  PersonalAccessToken::findToken($token);

        if ($personalAccessToken) {
            return new JsonResponse(['message' => 'Token is Valid', 'success' => true], 200);
        } else {
            return new JsonResponse(['message' => 'Token is Invalid', 'success' => false], 401);
        }
    }

    protected function verifyBeforeLogin(Request $request, User $user)
    {

        if ($user->email_verified_at == null) return $this->authUnverifiedUserErrorCode;

        if (Hash::check($request->password, $user->password)) {
            return $this->authSuccessCode;
        }
        return $this->authBasicErrorCode;
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $user = User::where($this->username(), $request->email)->first();

        if ($user == null) {
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
        }

        if ($authCode = $this->verifyBeforeLogin($request, $user)) {


            if ($authCode == $this->authUnverifiedUserErrorCode) return $this->sendUnVerifiedLoginResponse($request);
            // if ($authCode == $this->nonAllowedUserErrorCode) return $this->sendFailedLoginResponse($request);
            if ($authCode == $this->authBasicErrorCode) {
                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);
                return $this->sendFailedLoginResponse($request);
            }
            if ($authCode == $this->authSuccessCode) {
                $this->clearLoginAttempts($request);
                //revoking all token
                //$user->tokens()->delete();
                //logging in
                $token = $user->createToken($request->device . $user->id)->plainTextToken;

                return [
                    'user'  => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ];
            }
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user)
    {
        //
    }

    public function getAuthenticatedUser(Request $request)
    {
        if (!Auth::check()) abort(401);
        return new JsonResponse($this->generateAuthenticatedUserData(), 200);
    }



    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();


        return new JsonResponse([], 204);
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }
}
