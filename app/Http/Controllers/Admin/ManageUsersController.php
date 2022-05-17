<?php

namespace App\Helpers;

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Carbon\Carbon;
use DataTables;
use App\Helper\Helper;
use App\Mail\UserDetailsMail;
use DB;
use File;
use Storage;
use App\Models\Category;
use App\Models\UserAssignProduct;
use App\Models\UserSocialLink;
use Illuminate\Support\Facades\Password;
use vendor\autoload;
use JeroenDesloovere\VCard\VCard;
use Illuminate\Support\Facades\Mail;


class ManageUsersController extends Controller
{
    public function index(Request $request)
    {

        return view('Admin.user.index');
    }

    public function add(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'userName' => 'required',
            // 'email' => 'required',
            // 'password' => 'required',
            'phone' => 'required',
            'status' => 'required',
        ]);
        $update_id = $request->input('userHdnID');
        if (empty($update_id)) {
            $validation->password = 'required';
            $validation->email   = 'required|email';
        }
        if ($validation->fails()) {
            $data['status'] = 0;
            $data['error'] = $validation->errors()->all();
            echo json_encode($data);
            exit();
        }


        $userData = $request->all();
        $result['status'] = 0;
        $result['msg'] = "Something went wrong please try again";

        $check_user = User::where('email', $userData['email'])->first();
        if ($check_user == '' && $check_user == null || $update_id != '' && $update_id != null) {

            $insertData = new User;
            if ($update_id == '' && $update_id == null) {
                $insertData->name           = $userData['userName'];
                $insertData->email          = $userData['email'];
                $insertData->password       = Hash::make($userData['password']);
                $insertData->phone          = $userData['phone'];
                $insertData->status         = $userData['status'];
                $insertData->created_at     = Carbon::now()->timestamp;
                $insertData->save();
                $insert_id = $insertData->id;
                if ($insert_id > 0) {
                    $result['status'] = 1;
                    $result['msg'] = "User created Successfully";
                    // $result['id'] = $insert_id;
                }
            } else {
                $updateDetails = User::where('id', $update_id)->first();
                $updateDetails->name           = $userData['userName'];
                // $updateDetails->email          = $userData['email'];
                $updateDetails->password       = !empty($userData['password']) ? Hash::make($userData['password']) : $updateDetails->password;
                $updateDetails->phone          = $userData['phone'];
                $updateDetails->status         = $userData['status'];
                $updateDetails->is_block       = $userData['block'];
                $updateDetails->updated_at     = Carbon::now()->timestamp;
                $updateDetails->save();
                $result['status'] = 1;
                $result['msg'] = "User Data Update Successfully!";
            }
        } else {
            $result['status'] = 2;
            $result['msg'] = "E-mail Already Exist !";
        }
        echo json_encode($result);
        exit;
    }

    public function emailExistOrNot(Request $request)
    {
        $allData = $request->all();
        $user_email = $allData['email'];
        $hid = $request->input('userHdnID');
        $find_user = User::where('email', '=', $user_email)->where('status', '!=', '-1');
        if ($hid > 0) {
            $find_user->where('id', '!=', $hid);
        }
        $result = $find_user->count();

        if (isset($allData['forgot']) && $allData['forgot'] == 1 && $allData['forgot'] != '') {
            if ($result > 0) {
                echo json_encode(TRUE);
            } else {
                echo json_encode(FALSE);
            }
        } else {
            if ($result > 0) {
                echo json_encode(FALSE);
            } else {
                echo json_encode(TRUE);
            }
        }
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $data =  User::where('status', '!=', -1)->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $status = '<span class="badge badge-danger">Inactive</span>';
                    if ($row->status == 1) {
                        $status = '<span class="badge badge-success">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    // $action = '<input type="button" value="Delete" class="btn btn-sm btn-danger deleteUser" data-id="'. $row->id .'" ">';
                    $action = '<button type="button" class="btn btn-danger btn-sm btn-icon icon-left deleteUser" data-id="' . $row->id . '"><i class="entypo-cancel"></i> Delete</button>&nbsp;';
                    $action .= '<button type="button" class="btn btn-info btn-sm btn-icon icon-left editUser" data-id="' . $row->id . '"><i class="entypo-pencil"></i> Edit </button>&nbsp;';
                    $action .= '<button type="button" class="btn btn-primary btn-sm btn-icon icon-left blockUser" data-id="' . $row->id . '"><i class="entypo-block"></i> Block User </button>&nbsp;';
                    $action .= '<button type="button" class="btn btn-success btn-sm btn-icon icon-left resetPassword" data-id="' . $row->id . '"><i class="entypo-lock"></i> Reset Password </button>';

                    return $action;
                })

                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function edit(Request $request)
    {
        $edit_id = $request->input('id');
        $responsearray = array();
        $responsearray['status'] = 0;
        if ($edit_id != '' && $edit_id != null) {
            $edit_sql = User::where('id', $edit_id)->first();
            if ($edit_sql) {
                $responsearray['status'] = 1;
                $responsearray['userData'] = $edit_sql;
            }
        }
        echo json_encode($responsearray);
        exit;
    }

    public function delete(Request $request)
    {
        $delete_id = $request->input('id');
        $result['status'] = 0;
        $result['msg'] = "Oops ! User Not Deleted !";
        if ($delete_id != '' && $delete_id != null) {
            $userDetails = User::where('id', $delete_id)->first();
            $userDetails->status            =  -1;
            $userDetails->updated_at        = Carbon::now();
            $userDetails->save();
            if ($userDetails) {
                $result['status'] = 1;
                $result['msg'] = "User deleted successfully";
            }
        }
        echo json_encode($result);
        exit;
    }

    public function blockuser(Request $request)
    {
        $user_id = $request->input('id');
        $result['status'] = 0;
        $result['msg'] = "Oops ! User Not Deleted !";
        if ($user_id != '' && $user_id != null) {
            $userDetails = User::where('id', $user_id)->first();
            if ($userDetails->is_block != 1) {
                $userDetails->is_block          =  1;
                $userDetails->updated_at        = Carbon::now();
                $userDetails->save();
                if ($userDetails) {
                    $result['status'] = 1;
                    $result['msg'] = "User Blocked successfully";
                }
            } else {
                $result['status'] = 0;
                $result['msg'] = "User Already Blocked";
            }
        }
        echo json_encode($result);
        exit;
    }

    public function resetPassword(Request $request)
    {
        $user_id = $request->input('id');
        $result['status'] = 0;
        $result['msg'] = "Oops ! Password Reset Link Not Sended";

        if ($user_id != '' && $user_id != null) {
            $userDetails = User::select('email')->where('id', $user_id)->first();
            $emailAry['email'] = $userDetails->email;
            $resetlink = Password::sendResetLink($emailAry);
            if ($resetlink) {
                $result['status'] = 1;
                $result['msg'] = "Password Reset Link Sent successfully";
            }
        }
        echo json_encode($result);
        exit;
    }

    public function username($username)
    {
        $userInfo = User::where('username', $username)->where('status', 1)->where('is_block', 0)->first();
        if (isset($userInfo) && $userInfo != '') {
            $userInfo->social_links = UserSocialLink::where('user_id', $userInfo->id)->get()->toArray();
            // $card = $this->vcard($userInfo);
            return view('user-profile')->with(compact('userInfo'));
        } else {
            return view('notfound');
        }
    }

    public function vcard()
    {

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $path = parse_url($url, PHP_URL_PATH);
        $pathFragments = explode('/', $path);
        $username = end($pathFragments);

        $user = User::where('username', $username)->where('status', 1)->where('is_block', 0)->first();

        if (isset($user) && $user != '' && $user != null) {
            // define vcard
            $vcard = new VCard();

            // add personal data
            if (isset($user->name) && $user->name != '' && $user->name != null) {
                $vcard->addName($user->name);
            }
            // add work data
            if (isset($user->email) && $user->email != '' && $user->email != null) {
                $vcard->addEmail($user->email);
            }
            if (isset($user->phone) && $user->phone != '' && $user->phone != null) {
                $vcard->addPhoneNumber($user->phone, 'Phone Number');
            }
            if (isset($user->location) && $user->location != '' && $user->location != null) {
                $vcard->addAddress($user->location);
            }

            $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");
            $base_url .= "://" . $_SERVER['HTTP_HOST'];
            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
            $userurl = $base_url . $username;
            $vcard->addURL($userurl, 'EasyTap Digital Business Card');


            if (isset($user->social_links) && $user->social_links != '' && $user->social_links != null) {
                $phone  = array_filter($user->social_links, function ($var) {
                    return ($var['social_key'] == 'phone');
                });
                foreach ($phone as $key => $value) {
                    if (isset($value['social_value']) && $value['social_value'] != '' && $value['social_value'] != null) {
                        $vcard->addPhoneNumber($value['social_value'], 'Phone Number');
                    }
                }
            }
            if (isset($user->social_links) && $user->social_links != '' && $user->social_links != null) {

                $whats_app  = array_filter($user->social_links, function ($var) {
                    return ($var['social_key'] == 'whats_app');
                });
                foreach ($whats_app as $key => $value) {
                    if (isset($value['social_value']) && $value['social_value'] != '' && $value['social_value'] != null) {
                        $vcard->addURL($value['social_value'], 'WhatsApp');
                    }
                }
            }
            if (isset($user->profile_pic) && $user->profile_pic != '' && $user->profile_pic != null) {
                if (isset($user->profile_pic) && File::exists(public_path('uploads/profile_pic/' . $user->profile_pic))) {
                    $profile_pic = asset('/uploads/profile_pic/' . $user->profile_pic);
                    $vcard->addPhoto($profile_pic);
                }
            }

            // return vcard as a string
            // return $vcard->getOutput();

            // return vcard as a download
            return $vcard->download();

            // save vcard on disk
            // $vcard->setSavePath($dir);
            // $vcard->save();

            $result['msg'] = "Vcard Set Succsessful !";
        }
        $result['msg'] = "Oops! Something Went Wrong!";
    }

    public function senddata(Request $request)
    {

        $result['status'] = 0;
        $result['msg'] = "Oops!, Something Went Wrong !";

        $useremail = $request->input('hdnmail');
        $name = $request->input('name');
        $email = $request->input('email');
        $phonenumber = $request->input('phonenumber');

        if ($request->input('job') != '' && $request->input('job') != null) {
            $job = $request->input('job');
        } else {
            $job = '';
        }
        if ($request->input('company') != '' && $request->input('company') != null) {
            $company = $request->input('company');
        } else {
            $company = '';
        }
        if ($request->input('note') != '' && $request->input('note') != null) {
            $note = $request->input('note');
        } else {
            $note = '';
        }
        if (isset($useremail) && $useremail != '' && $useremail != null) {
            $data = [
                'subject'      => 'User Details Mail',
                'name'         => $name,
                'email'        => $email,
                'useremail'    => $useremail,
                'phonenumber'  => $phonenumber,
                'job'          => $job,
                'company'      => $company,
                'note'         => $note,
            ];
            Mail::to($data['useremail'])->send(new UserDetailsMail($data));
            $result['status'] = 1;
            $result['msg'] = "Your Details Sent successfully";
        }
        echo json_encode($result);
        exit;
    }

    public function downloaddoc()
    {

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $path = parse_url($url, PHP_URL_PATH);
        $pathFragments = explode('/', $path);
        $filename = end($pathFragments);

        if (isset($filename) && $filename != '' && $filename != null) {

            if (File::exists(public_path('uploads/document/' . $filename))) {
                $file = Storage::disk('uploadedfiles')->download($filename);
                return $file;
            } else {
                echo "<p style='font-size:40px; text-align: center;margin-top: 210px;'>Oops! file not found</p>";
            }
        } else {
            echo "<p style='font-size:40px; text-align: center;margin-top: 210px;'>Oops! Something Went Wrong!
            </br>Please try again later
            </br><b>by: Team Easytap.</b></p>";
        }
    }
}
