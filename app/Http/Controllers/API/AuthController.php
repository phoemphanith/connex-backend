<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * handle user registration request
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        $image = Upload::saveFile('/user', $request->file('profile'), null);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'profile' => $image
        ]);

        $access_token_example = $user->createToken('PassportExample@Section.io')->accessToken;
        //return the access token we generated in the above step
        return response()->json([
            'user' => $user,
            'token' => $access_token_example
        ], 200);
    }

    /**
     * login user to our application
     */
    public function login(Request $request)
    {
        $login_credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (auth()->attempt($login_credentials)) {
            $user = User::find(Auth::user()->id);
            $user_login_token = $user->createToken('PassportExample@Section.io')->accessToken;
            return response()->json([
                'user' => $user,
                'token' => $user_login_token
            ], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised Access'], 401);
        }
    }

    /**
     * This method returns authenticated user details
     */
    public function show()
    {
        return response()->json(['user' => auth()->user()], 200);
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $image = Upload::saveFile('/user', $request->profile, $user->profile);
        $user->update([
            'name' => request('name', $user->name),
            'profile' => $image
        ]);
        return response()->json(['user' => $user, 'message' => true, 'info' => 'Update Successfully.']);
    }

    public function changePassword(Request $request)
    {
        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->old_password, $hashedPassword)) {
            $user = User::find(Auth::user()->id);
            $user->update([
                'password' => bcrypt($request->password)
            ]);
            return response()->json([
                'message' => true,
                'info' => 'Password has been change.'
            ], 200);
        } else {
            return response()->json([
                'message' => false,
                'info' => 'Password can\'t change.'
            ], 202);
        }
    }
}
