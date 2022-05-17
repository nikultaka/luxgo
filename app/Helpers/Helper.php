<?php

//namespace App\Http\Controllers\Auth;

namespace App\Helper;

use Auth;
use Carbon\Carbon;
use App\Models\UserSocialLink;

class Helper
{

    public static function checkUserSocialLinkValueAvailableOrNot($key)
    {
        if ($key != '' && $key != null) {
            $val = UserSocialLink::select()->where('social_key', $key)->first();
            if ($val != '' && $val != null) {
                return $val;
            } else {
                return '';
            }
        }
        return '';
    }


    public function getSettingValueByName($key)
    {
        if ($key != '' && $key != null) {
            $val = UserSocialLink::select("social_value")->where('social_key', $key)->first();
            if (isset($val) && $val != '') {
                return $val->social_value;
            } else {
                return '';
            }
        }
        return '';
    }
}
