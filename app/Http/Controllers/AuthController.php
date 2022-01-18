<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;
use Mockery\Exception;

class AuthController extends Controller
{

    public function __construct()
    {
    }

    public function test()
    {
        return 1;
    }

    public function index()
    {
        return response('کاربر غیر مجاز است', 401);
    }

    public function otp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => ['required', 'min:11', 'max:11', 'regex:/(09)[0-9]{9}/'],
                'type' => ['nullable', 'boolean']
            ]);

            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }

            if ($request->type) {
                $validator = Validator::make($request->all(), [
                    'mobile' => ['required', 'min:11', 'max:11', 'regex:/(09)[0-9]{9}/', 'unique:users'],
                ]);

                if ($validator->fails()) {
                    return response($validator->errors(), 400);
                }

                $user = $this->repository->storeUser($request);
                if (!config('app.debug')) {
                    $user->sendVerificationCode();
                }

                return response('رمز یکبار مصرف ارسال شد', 200);
            } else {
                $user = User::whereMobile($request->mobile)->first();;

                if (!$user) {
                    $validator->errors()->add('mobile', 'شماره همراه وارد شده یافت نشد');
                    return response($validator->errors(), 400);
                } else {
                    if (!config('app.debug')) {
                        $user->sendVerificationCode();
                    }

                    return response('رمز یکبار مصرف ارسال شد', 200);
                }
            }
        } catch (Exception $exception) {
            return responseServerError();
        }
    }


    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'min:11', 'max:11', 'regex:/(09)[0-9]{9}/'],
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $user = User::whereMobile($request->mobile)->first();

        if (!$user) {
            $validator->errors()->add('mobile', 'شماره همراه وارد شده یافت نشد');
            return response($validator->errors(), 400);
        } else {
            if (!config('app.debug')) {
                $user->sendVerificationCode();
            }

            return response('رمز یکبار مصرف ارسال شد', 200);
        }
    }


    public function token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'min:11', 'max:11', 'regex:/(09)[0-9]{9}/'],
            'otp' => ['required', 'min:6', 'max:6'],
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $user = User::whereMobile($request->mobile)->first();

        if (!$user) {
            $validator->errors()->add('mobile', 'شماره همراه وارد شده یافت نشد');

            return response($validator->errors(), 400);
        }

        if (config('app.debug') or $user->checkOtp($request->otp)) {
            $client = Client::where('password_client', 1)->first();
            $request->request->add([
                "grant_type" => "password",
                "username" => $request->mobile,
                "password" => $request->otp,
                "client_id"     => $client->id,
                "client_secret" => $client->secret,
            ]);
            $tokenRequest = $request->create(
                '/oauth/token',
                'post'
            );
            $instance = Route::dispatch($tokenRequest);
            $tokenInfo = json_decode($instance->getContent(), true);
            $tokenInfo = collect($tokenInfo);

            if ($tokenInfo->has('error')) {
                return response(['message' => 'کاربر غیر مجاز است', 'status' => 401], 401);
            }

            $user_info = [
                'mobile' => $user->mobile,
            ];
            $tokenInfo['user'] = $user_info;
            return response($tokenInfo, 200);
        } else {
            $validator->errors()->add('code', 'کد وارد شده معتبر نمی باشد');

            return response($validator->errors(), 401);
        }
    }


    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'min:11', 'max:11', 'regex:/(09)[0-9]{9}/'],
            'refresh_token' => ['required']
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $user = User::whereMobile($request->mobile)->first();

        if (!$user) {
            $validator->errors()->add('mobile', 'شماره همراه وارد شده یافت نشد');

            return response($validator->errors(), 400);
        }

        $client = Client::where('password_client', 1)->first();
        $request->request->add([
            "grant_type" => "refresh_token",
            "refresh_token" => $request->refresh_token,
            "client_id"     => $client->id,
            "client_secret" => $client->secret,
        ]);
        $tokenRequest = $request->create(
            '/oauth/token',
            'post'
        );
        $instance = Route::dispatch($tokenRequest);
        $tokenInfo = json_decode($instance->getContent(), true);
        $tokenInfo = collect($tokenInfo);

        if ($tokenInfo->has('error')) {
            return response('کاربر غیر مجاز است', 401);
        }

        $user_info = [
            'mobile' => $user->mobile,
        ];

        $tokenInfo['user'] = $user_info;
        return response($tokenInfo, 200);
    }


    public function revokeToken()
    {
        $user = Auth::user();
        $user->token()->revoke();

        return response('با موفقیت خارج شدید', 200);
    }


    public function checkToken()
    {
        return response('توکن شما معتبر است', 200);
    }

}
