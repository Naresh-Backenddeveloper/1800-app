<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required'
        ]);

        $request->user()->update([
            'fcm_token' => $request->fcm_token
        ]);

        return response()->json(['code' => 0, 'message' => 'Token saved']);
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'mobile' => 'required|max:10',
        ])->passes();
        if (!$validated) {
            return response()->json(['code' => 1, 'message' => 'Invalid Request']);
        }
        $mobile = $request->input('mobile');
        $user = User::where('mobile', $mobile)->where('role', 'USER')->first();
        if ($mobile === getenv('TEST_MOBILE_NUMBER')) {
            if ($user == null) {
                $otp = getenv('TEST_OTP');
                $otp_expires_at = now()->addMinutes(10);
                $data = [
                    'mobile' => $mobile,
                    'otp' => $otp,
                    'role' => "USER",
                    'created_at' => now(),
                    'updated_at' => now(),
                    'otp_expires_at' => $otp_expires_at,
                ];
                $id = User::insertGetId($data);

                if ($id) {
                    return response()->json([
                        'code' => 0,
                        'message' => 'OK',
                    ]);
                }

                return response()->json([
                    'code' => 1,
                    'message' => 'INVALIED',
                ]);
            } else {
                $otp = getenv('TEST_OTP');
                $otp_expires_at = now()->addMinutes(10);
                $user->otp = $otp;
                $user->otp_expires_at = now()->addMinutes(10);
                $user->save();
                return response()->json([
                    'code' => 0,
                    'message' => 'OK',
                ]);
            }
        }
        if ($user == null) {
            $data = [];
            // $otp = $this->generateOTP();
            $otp = 1234;
            $data['otp'] = $otp;
            $type = "LOGIN_OTP";
            $to = $request->mobile;
            // $result1 = $this->smsService->sendSms($type, $to, $data, $gateway = 1);
            // if (!$result1) {
            //     return response()->json([
            //         'code' => 1,
            //         'message' => 'OTP_SEND_FAILED',
            //     ]);
            // }
            $otp_expires_at = now()->addMinutes(10);
            $data = [
                'mobile' => $mobile,
                'otp' => $otp,
                'role' => "USER",
                'created_at' => now(),
                'updated_at' => now(),
                'otp_expires_at' => $otp_expires_at,
            ];

            $id = User::insertGetId($data);

            if ($id) {
                return response()->json([
                    'code' => 0,
                    'message' => 'OK',
                ]);
            }

            return response()->json([
                'code' => 1,
                'message' => 'INVALIED',
            ]);
        }
        // $otp = $this->generateOTP();
        $otp = 1234;
        $data['otp'] = $otp;
        $type = "LOGIN_OTP";
        $to = $request->mobile;
        // $result1 = $this->smsService->sendSms($type, $to, $data, $gateway = 1);
        // if (!$result1) {
        //     return response()->json([
        //         'code' => 1,
        //         'message' => 'OTP_SEND_FAILED',
        //     ]);
        // }
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();
        return response()->json([
            'code' => 0,
            'message' => 'OK',
        ]);
    }


    public function userVerfy(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'mobile' => 'required|max:10',
            'otp' => 'required',
        ])->passes();
        if (!$validated) {
            return response()->json(['code' => 1, 'message' => 'Invalid Request']);
        }
        $mobile = $request->mobile;
        $otp = $request->otp;

        $user = User::where('mobile', $mobile)->where('otp', $otp)->first();

        if ($user == null) {
            return response()->json(['code' => 1, 'message' => 'Invalid']);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['code' => 1, 'message' => 'OTP mismatch or expired']);
        }
        $user->verify_flag = 1;
        $user->update();

        $token = $user->createApiToken('API Access');

        return response()->json(['code' => 0, 'message' => 'OK', 'data' => ['token' => $token, 'profile_flag' => $user->profile_flag]]);
    }

    public function states()
    {
        $data = DB::table('states')->where('active_flag', '1')->get();
        return response()->json(['code' => 0, 'message' => 'OK', 'data' => $data]);
    }

    public function districts($id)
    {
        $data = DB::table('districts')->where('state_id', $id)->get();
        return response()->json(['code' => 0, 'message' => 'OK', 'data' => $data]);
    }
}
