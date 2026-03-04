<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Upload_Images;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    protected $uploadImages;

    public function __construct(Upload_Images $uploadImages)
    {
        $this->uploadImages = $uploadImages;
    }

    public function UpdateProfile(Request $request)
    {
        $user = $request->user();

        $validated = Validator::make($request->all(), [
            'profile_image' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'location' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable'
        ]);

        if ($validated->fails()) {
            return response()->json(['code' => 1, 'message' => 'Invalid Request', 'errors' => $validated->errors()]);
        }
        $file = $request->profile_image;
        $image_url = $this->uploadImages->storageFilepath($file);

        $userData = [
            'name' => $request->Username,
            'email' => $request->email,
            'profile_pic' => $image_url,
            'address' => $request->location,
            'latitude' => $request->lattitude,
            'longitude' => $request->longitude,
            'updated_at' => Carbon::now(),
            'profile_flag'=>1
        ];
        $result = User::where('id', $user->id)->update($userData);
        if ($result) {
            return response()->json([
                'code'    => 0,
                'message' => 'OK',
            ]);
        }
        return response()->json([
            'code'    => 1,
            'message' => 'INVALID',
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();

        $user->profile_pic = $user->profile_pic ? url('cloud/' . $user->profile_pic) : " ";

        return response()->json([
            'code'    => 0,
            'message' => 'OK',
            'data'    => $user
        ]);
    }

    

}
