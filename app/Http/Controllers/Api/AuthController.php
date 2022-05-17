<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
// use Illuminate\Support\Facades\Auth;
use App\Models\User;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\File;
use PKPass\PKPass;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    //Login Check
    public function isEmailExists(Request $request)
    {
        $credentials = $request->only('email', 'type', 'apple_id');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required_if:type,!=,3|email',
            'type' => 'required',
            'apple_id' => 'required_if:type,==,3'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        if ($credentials['type'] != '3') {
            $currentUser_email =  $credentials['email'];
            $check_user = User::where('email', $currentUser_email)->first();
        } else {
            $currentUser_email =  $credentials['apple_id'];
            $check_user = User::where('apple_id', $currentUser_email)->first();
        }


        if ($check_user == null && $check_user == '') {
            if ($credentials['type'] != '3') {
                return response()->json([
                    'success' => true,
                    'message' => "Email Id Available For Registration",
                    'data' => $currentUser_email
                ], 201);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => "Apple Id Available For Registration",
                    'data' => $currentUser_email
                ], 201);
            }
        } else {
            if ($credentials['type'] != '3') {
                return response()->json([
                    'success' => false,
                    'message' => "Email Id Already Taken",
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Apple Id Already Taken",
                ], 200);
            }
        }
    }


    // Login user
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password', 'type', 'apple_id');

        $validator = Validator::make($credentials, [
            'email' => 'required_if:type,==,1,2|email',
            'type' => 'required',
            'password' => 'required_if:type,==,1',
            'apple_id' => 'required_if:type,==,3'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }
        try {
            $currentUser_email = '';
            if ($credentials['type'] == '1' || $credentials['type'] == '2') {
                if (isset($credentials['email'])) {
                    $currentUser_email =  $credentials['email'];
                }
                $check_user = User::where('email', $currentUser_email)->first();
            } else {
                if (isset($credentials['apple_id'])) {
                    $currentUser_email =  $credentials['apple_id'];
                }
                $check_user = User::where('apple_id', $currentUser_email)->first();
            }

            if (isset($check_user->is_block) && $check_user->is_block == 1) {
                return response()->json([
                    'success' => false,
                    'message' => "User Is Block by Admin.",
                ], 200);
            } else {

                if ($credentials['type'] == '1' || $credentials['type'] == '2') {
                    $check_user = User::where('email', $currentUser_email)->where('status', '!=', '-1')->first();
                } else {
                    $check_user = User::where('apple_id', $currentUser_email)->where('status', '!=', '-1')->first();
                }

                if ($check_user == null && $check_user == '') {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid email Id",
                    ], 200);
                }
                if ($check_user->status == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "User Is block by Admin.",
                    ], 200);
                }


                $type =  $request['type'];
                if ($type == 1 && $type != null && $type != '') {

                    $credentials = $request->only('email', 'password');
                    $validator = Validator::make($credentials, [
                        'password' => 'required|min:6'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(['error' => $validator->messages()]);
                    }

                    if (!$token = JWTAuth::attempt($credentials)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Login credentials are invalid.',
                        ], 200);
                    }
                }
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 200);
        }


        if ($type == 1 && $type != null && $type != '') {

            $email =  $request['email'];
            $check_user = User::select('type')->where('email', $email)->where('status', '!=', '-1')->first();

            if ($check_user->type == 1  && $check_user->type != null && $check_user->type != '') {
                $currentUser = Auth::user();
                $currentUser->token = $token;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 200);
            }
        } else if ($type == 2 && $type != null && $type != '') {

            $email =  $request['email'];
            $check_user = User::select('type')->where('email', $email)->where('status', '!=', '-1')->first();

            if ($check_user->type == 2  && $check_user->type != null && $check_user->type != '') {
                $currentUser = User::where('email', '=', $request->email)->first();
                $token = JWTAuth::fromUser($currentUser);
                $currentUser->token = $token;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 200);
            }
        } else if ($type == 3 && $type != null && $type != '') {

            $email =  $request['apple_id'];
            $check_user = User::select('type')->where('apple_id', $email)->where('status', '!=', '-1')->first();

            if ($check_user->type == 3  && $check_user->type != null && $check_user->type != '') {
                $currentUser = User::where('apple_id', '=', $request->apple_id)->first();
                $token = JWTAuth::fromUser($currentUser);
                $currentUser->token = $token;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 200);
            }
        }

        if (!empty($currentUser) && isset($currentUser->email_verified_at) && $currentUser->email_verified_at == null) {
            $currentUser->is_email_verified = 0;
        } else {
            $currentUser->is_email_verified = 1;
        }
        unset($currentUser->firstname);
        unset($currentUser->lastname);
        //unset($currentUser->username);
        //unset($currentUser->dob);
        return response()->json([
            'success' => true,
            'message' => "User LoggedIn Successfully",
            //'token' => $token,
            'data' => $currentUser
        ], 201);
    }

    /**
     * Registration Req
     */

    public function uploadFile($request)
    {
        if (!is_dir(PROFILE_IMAGE_PATH)) {
            $path = public_path(PROFILE_IMAGE_PATH);
            mkdir($path, null, true);
        }
        $profilePic = '';
        $image = basename($request->file('profile_pic')->getClientOriginalName(), '.' . $request->file('profile_pic')->getClientOriginalExtension());
        if ($image) {
            $new_image_name = uniqid() . '_' . time();
            $profilePic = $new_image_name . '.' . $request->file('profile_pic')->getClientOriginalExtension();
            $destinationPath = public_path(PROFILE_IMAGE_PATH);
            $request->file('profile_pic')->move($destinationPath, $profilePic);
        }
        return $profilePic;
    }

    public function register(Request $request)
    {
        $req = $request->only('name', 'email', 'password', 'phone', 'type', 'username', 'profile_pic', 'apple_id');

        $validator = Validator::make($req, [
            'email'      => 'required_if:type,==,1,2|email|unique:users',
            // 'password' => 'min:6',
            'type'       => 'required',
            'apple_id'   => 'required_if:type,==,3|unique:users,apple_id'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $proileImage = '';
        if (isset($request->profile_pic) && $request->profile_pic != '') {
            $proileImage = $request->profile_pic;
        }

        if ($request->type != 3) {
            $find_user = User::where('email', $req['email'])->count();
        } else {
            $find_user = User::where('apple_id', $req['apple_id'])->count();
        }

        if ($find_user == 0) {

            if ($request->type != 3) {
                $email =  $req['email'];
                $username = strstr($email, '@', true);
            } else {
                $username = uniqid();
            }

            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < 4; $i++) {
                $index = rand(0, strlen($characters) - 1);
                $randomString .= $characters[$index];
            }
            if (isset($username) && $username != '' && isset($randomString) && $randomString != '') {
                $finalstring = $username . $randomString;

                $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");
                $base_url .= "://" . $_SERVER['HTTP_HOST'];
                $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
                $url = $base_url . $finalstring;
            }


            if (isset($finalstring) && $finalstring != '') {
                $check_user = User::where('username', $finalstring)->count();
                if ($check_user == 0) {

                    $type =  $req['type'];

                    if ($type == 1 && $type != null && $type != '') {

                        $credentials = $request->only('password');
                        $validator = Validator::make($credentials, [
                            'password' => 'required|min:6',
                            'profile_pic' => 'mimes:jpeg,jpg,png|max:2000|nullable'
                        ]);
                        if ($validator->fails()) {
                            return response()->json(['error' => $validator->messages()]);
                        }

                        $profilePic = '';
                        if (isset($request['profile_pic']) && !empty($request->file('profile_pic'))) {
                            $profilePic = $this->uploadFile($request);
                        }

                        $user = User::create([
                            'name'          => $request->name,
                            'email'         => $request->email,
                            'phone'         => $request->phone,
                            'password'      => Hash::make($request->password),
                            'type'          => $type,
                            'username'      => $finalstring,
                            'profile_pic'   => $profilePic,
                            'status'        => 1
                        ]);
                        if ($user) {
                            $credentials = $request->only('email', 'password');
                            $token = JWTAuth::attempt($credentials);
                            $user->token = $token;
                            return response()->json([
                                'success' => true,
                                'message' => "You have registered successfully.",
                                'data' => $user
                            ], 201);
                        }
                    } elseif ($type == 2 && $type != null && $type != '') {

                        $credentials = $request->only('password');
                        $validator = Validator::make($credentials, [
                            'profile_pic' => 'mimes:jpeg,jpg,png|max:2000|nullable'
                        ]);
                        if ($validator->fails()) {
                            return response()->json(['error' => $validator->messages()]);
                        }

                        $profilePic = '';
                        if (isset($request['profile_pic']) && !empty($request->file('profile_pic'))) {
                            $profilePic = $this->uploadFile($request);
                        }

                        $user = User::create([
                            'name'          => $request->name,
                            'email'         => $request->email,
                            'phone'         => $request->phone,
                            'password'      => null,
                            'type'          => $type,
                            'profile_pic'   => $profilePic,
                            'username'      => $finalstring,
                            'status'        => 1
                        ]);
                        if ($user) {
                            $userEmail = User::where('email', '=', $request->email)->first();
                            $userToken = JWTAuth::fromUser($userEmail);
                            $user->token = $userToken;
                            return response()->json([
                                'success' => true,
                                'message' => "You have registered successfully With Google Login",
                                'data' => $user
                            ], 201);
                        }
                    } elseif ($type == 3 && $type != null && $type != '') {
                        $credentials = $request->only('password');
                        $validator = Validator::make($credentials, [
                            'profile_pic' => 'mimes:jpeg,jpg,png|max:2000|nullable'
                        ]);

                        if ($validator->fails()) {
                            return response()->json(['error' => $validator->messages()]);
                        }

                        $profilePic = '';
                        if (isset($request['profile_pic']) && !empty($request->file('profile_pic'))) {
                            $profilePic = $this->uploadFile($request);
                        }

                        $user = User::create([
                            'name'          => $request->name,
                            'email'         => $request->email,
                            'phone'         => $request->phone,
                            'password'      => null,
                            'type'          => $type,
                            'profile_pic'   => $profilePic,
                            'username'      => $finalstring,
                            'status'        => 1,
                            'apple_id'      => $request->apple_id,
                        ]);

                        if ($user) {
                            $userEmail = User::where('apple_id', '=', $request->apple_id)->first();
                            $userToken = JWTAuth::fromUser($userEmail);
                            $user->token = $userToken;
                            return response()->json([
                                'success' => true,
                                'message' => "You have registered successfully With Apple Login",
                                'data' => $user
                            ], 201);
                        }
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Something went wrong please try again",
                    ], 400);
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "The email address you entered has already been registered.",
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => "Something went wrong please try again",
        ], 400);
    }

    public function profileImage(Request $request)
    {


        $credentials = $request->only('profile_pic');

        //valid credential
        $validator = Validator::make($credentials, [
            'profile_pic' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }


        $currentUserId = $request->id;
        if (!$currentUserId) {

            if (isset($request['profile_pic'])) {

                if (!is_dir(PROFILE_IMAGE_PATH)) {
                    $path = public_path(PROFILE_IMAGE_PATH);
                    mkdir($path, null, true);
                }

                $image = basename($request->file('profile_pic')->getClientOriginalName(), '.' . $request->file('profile_pic')->getClientOriginalExtension());

                if ($image) {
                    $new_image_name = uniqid() . '_' . time();
                    $fileName = $new_image_name . '.' . $request->file('profile_pic')->getClientOriginalExtension();
                    $destinationPath = public_path(PROFILE_IMAGE_PATH);
                    $request->file('profile_pic')->move($destinationPath, $fileName);
                } else {
                    $fileName = "";
                }
            }

            if (isset($request['profile_pic'])) {

                if ($fileName) {
                    return response()->json([
                        'success' => true,
                        'message' => "Profile Image registered successfully.",
                        'data' => $fileName
                    ], 201);
                }
            }
        } else {

            if (isset($request['profile_pic'])) {

                if (!is_dir(PROFILE_IMAGE_PATH)) {
                    $path = public_path(PROFILE_IMAGE_PATH);
                    mkdir($path, null, true);
                }

                $image = basename($request->file('profile_pic')->getClientOriginalName(), '.' . $request->file('profile_pic')->getClientOriginalExtension());

                if ($image) {
                    $new_image_name = uniqid() . '_' . time();
                    $fileName2 = $new_image_name . '.' . $request->file('profile_pic')->getClientOriginalExtension();
                    $destinationPath = public_path(PROFILE_IMAGE_PATH);
                    $request->file('profile_pic')->move($destinationPath, $fileName2);
                } else {
                    $fileName2 = "";
                }
            }

            $imageexist = User::where('id',  $currentUserId)->first();
            if (isset($imageexist->profile_pic) && isset($request['profile_pic']) && file_exists(public_path(PROFILE_IMAGE_PATH . $imageexist->profile_pic))) {
                unlink(public_path(PROFILE_IMAGE_PATH . $imageexist->profile_pic));
            }

            $user = User::where('id',  $currentUserId)->first();

            if (isset($request['profile_pic'])) {
                $user->profile_pic = $fileName2;
            }

            if ($user->update()) {
                return response()->json([
                    'success' => true,
                    'message' => "Profile Image Updated Successfully",
                    'data' => $user
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Profile Image Not Updated",
                ], 200);
            }
        }
    }

    public function updateProfile(Request $request)
    {
        $credentials = $request->only('name', 'phone', 'email', 'location', 'profile_pic', 'personal_info', 'personal_logo', 'background_image', 'dob', 'education', 'gender', 'profession', 'company','gender_show','dob_show');

        //valid credential
        $validator = Validator::make($credentials, [
            //'name' => 'required',
            // 'firstname' => 'required',
            // 'lastname' => 'required',
            // 'phone' => 'required',
            'dob' => 'date_format:Y-m-d',
            // 'phone' => 'required',
            'personal_logo' => 'mimes:jpeg,jpg,png|max:2000|nullable',
            'background_image' => 'mimes:jpeg,jpg,png|max:2000|nullable',
            'profile_pic' => 'mimes:jpeg,jpg,png|max:2000|nullable'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }

        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => "User Not Loged In",
            ], 200);
        }

        $currentUserId = $currentUser->id;
        if (!$currentUserId) {
            return response()->json([
                'success' => false,
                'message' => "User Not Found",
            ], 200);
        }


        $user = User::where('id',  $currentUserId)->first();
        if (isset($request['name']) && $request['name'] != '' && $request['name'] != null) {
            $user->name = $credentials['name'];
        }

        if (isset($request['email']) && $request['email'] != '' && $request['email'] != null) {
            $user->email = $credentials['email'];
        }

        if (isset($request['phone']) && $request['phone'] != '' && $request['phone'] != null) {
            $user->phone = $credentials['phone'];
        }

        if (isset($request['location']) && $request['location'] != '' && $request['location'] != null) {
            $user->location = $credentials['location'];
        }

        if (isset($request['personal_info']) && $request['personal_info'] != '' && $request['personal_info'] != null) {
            $user->personal_info = $credentials['personal_info'];
        }

        if (isset($request['dob']) && $request['dob'] != '' && $request['dob'] != null) {
            $user->dob = $credentials['dob'];
        }

        if (isset($request['education']) && $request['education'] != '' && $request['education'] != null) {
            $user->education = $credentials['education'];
        }

        if (isset($request['gender']) && $request['gender'] != '' && $request['gender'] != null) {
            $user->gender = $credentials['gender'];
        }

        if (isset($request['profession']) && $request['profession'] != '' && $request['profession'] != null) {
            $user->profession = $credentials['profession'];
        }

        if (isset($request['company']) && $request['company'] != '' && $request['company'] != null) {
            $user->company = $credentials['company'];
        }

        if (isset($request['gender_show']) && $request['gender_show'] != '' && $request['gender_show'] != null) {
            $user->gender_show = $credentials['gender_show'];
        }        
        
        if (isset($request['dob_show']) && $request['dob_show'] != '' && $request['dob_show'] != null) {
            $user->dob_show = $credentials['dob_show'];
        }

        $fileName = "";
        if (isset($request['personal_logo']) && !empty($request->file('personal_logo'))) {

            if (!is_dir(PERSONAL_LOGO_PATH)) {
                $path = public_path(PERSONAL_LOGO_PATH);
                mkdir($path, null, true);
            }
            $image = basename($request->file('personal_logo')->getClientOriginalName(), '.' . $request->file('personal_logo')->getClientOriginalExtension());
            if ($image) {
                $old_img_path = public_path(PERSONAL_LOGO_PATH) . $user->personal_logo;
                if (File::exists($old_img_path)) {
                    File::delete($old_img_path);
                }
                $new_image_name = uniqid() . '_' . time();
                $fileName = $new_image_name . '.' . $request->file('personal_logo')->getClientOriginalExtension();
                $destinationPath = public_path(PERSONAL_LOGO_PATH);
                $request->file('personal_logo')->move($destinationPath, $fileName);
                $user->personal_logo = $fileName;
            }
        }

        $fileName2 = "";
        if (isset($request['background_image']) && !empty($request->file('background_image'))) {

            if (!is_dir(BACKGROUND_IMAGE_PATH)) {
                $path = public_path(BACKGROUND_IMAGE_PATH);
                mkdir($path, null, true);
            }
            $image = basename($request->file('background_image')->getClientOriginalName(), '.' . $request->file('background_image')->getClientOriginalExtension());
            if ($image) {
                $old_img_path = public_path(BACKGROUND_IMAGE_PATH) . $user->background_image;
                if (File::exists($old_img_path)) {
                    File::delete($old_img_path);
                }
                $new_image_name = uniqid() . '_' . time();
                $fileName2 = $new_image_name . '.' . $request->file('background_image')->getClientOriginalExtension();
                $destinationPath = public_path(BACKGROUND_IMAGE_PATH);
                $request->file('background_image')->move($destinationPath, $fileName2);
                $user->background_image = $fileName2;
            }
        }

        $fileName3 = "";
        if (isset($request['profile_pic']) && !empty($request->file('profile_pic'))) {

            if (!is_dir(PROFILE_IMAGE_PATH)) {
                $path = public_path(PROFILE_IMAGE_PATH);
                mkdir($path, null, true);
            }
            $image = basename($request->file('profile_pic')->getClientOriginalName(), '.' . $request->file('profile_pic')->getClientOriginalExtension());
            if ($image) {
                $old_img_path = public_path(PROFILE_IMAGE_PATH) . $user->profile_pic;
                if (File::exists($old_img_path)) {
                    File::delete($old_img_path);
                }
                $new_image_name = uniqid() . '_' . time();
                $fileName3 = $new_image_name . '.' . $request->file('profile_pic')->getClientOriginalExtension();
                $destinationPath = public_path(PROFILE_IMAGE_PATH);
                $request->file('profile_pic')->move($destinationPath, $fileName3);
                $user->profile_pic = $fileName3;
            }
        }

        if ($user->update()) {
            unset($user->firstname);
            unset($user->lastname);
            unset($user->username);
            //unset($user->dob);
            return response()->json([
                'success' => true,
                'message' => "Profile Updated Successfully",
                'data' => $user
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Profile Not Updated",
            ], 200);
        }
    }

    public function updatePassword(Request $request)
    {
        $credentials = $request->only('current_password', 'new_password');
        $validator = Validator::make($credentials, [
            'current_password' => 'required',
            'new_password' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }
        $currentUser = Auth::user();

        $currentUserId = $currentUser->id;
        if (!$currentUserId) {
            return response()->json([
                'success' => false,
                'message' => "User Not Found",
            ], 200);
        }
        $user = User::where('id',  $currentUserId)->first();
        if (Hash::check($credentials['current_password'], $user->password)) {
            if (strlen($credentials['new_password']) < 6) {
                return response()->json([
                    'success' => false,
                    'message' => "New Password must be 6 charecters long",
                ], 200);
            }
            $user->password =  Hash::make($credentials['new_password']);
            if ($user->update()) {
                return response()->json([
                    'success' => true,
                    'message' => "Password Updated Successfully",
                ], 201);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Current password is invalid",
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::setToken($token)->invalidate();
            return response()->json([
                'success' => true,
                'message' => "User successfully signed out",
            ], 201);
        }
        return response()->json([
            'success' => false,
            'message' => "Something went wrong please try again",
        ], 400);
    }

    public function forgot_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        } else {
            try {
                $user = User::where('email', '=', $request->only('email'))->first();
                if ($user === null) {
                    return response()->json([
                        'success' => false,
                        'message' => "User not exist",
                    ], 200);
                } else {
                    $response = Password::sendResetLink($request->only('email'));
                    return response()->json([
                        'success' => true,
                        'message' => "Reset password link sent to your emailid",
                    ], 201);
                }
            } catch (\Swift_TransportException $ex) {
                return response()->json([
                    'success' => false,
                    'message' => $ex->getMessage(),
                ], 200);
            } catch (Exception $ex) {
                return response()->json([
                    'success' => false,
                    'message' => $ex->getMessage(),
                ], 200);
            }
        }
    }

    public function userProfile(Request $request)
    {
        $currentUser = Auth::user();
        $currentUserId = $currentUser->id;
        if (!$currentUserId) {
            return response()->json([
                'success' => false,
                'message' => "User Not Found",
            ], 200);
        }

        $user = User::where('id',  $currentUserId)->first();

        if ($user->update()) {
            return response()->json([
                'success' => true,
                'message' => "Profile info get Successfully",
                'data' => $user
            ], 201);
        }
    }

    public function changePassword(Request $request)
    {
        $credentials = $request->only('old_password', 'new_password');
        $validator = Validator::make($credentials, [
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()]);
        }
        $currentUser = Auth::user();

        $currentUserId = $currentUser->id;
        if (!$currentUserId) {
            return response()->json([
                'success' => false,
                'message' => "User Not Found",
            ], 200);
        }


        $user = User::where('id',  $currentUserId)->first();
        if (isset($user['type']) && $user['type'] == 1) {
            if (isset($user['password']) && $user['password'] != null && $user['password'] != '') {
                if (Hash::check($credentials['old_password'], $user->password)) {
                    if (strlen($credentials['new_password']) < 6) {
                        return response()->json([
                            'success' => false,
                            'message' => "New Password must be 6 charecters long",
                        ], 200);
                    }
                    $user->password =  Hash::make($credentials['new_password']);
                    if ($user->update()) {
                        return response()->json([
                            'success' => true,
                            'message' => "Password Updated Successfully",
                        ], 201);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Old password is invalid",
                    ], 200);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "password is Empty",
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "You login As Google Login or Apple Login",
            ], 200);
        }
    }
}
