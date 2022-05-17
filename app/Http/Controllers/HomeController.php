<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;
use App\Models\User;
use App\Models\UserSocialLink;
use App\Helper\Helper;
use Kreait\Firebase\ServiceAccount;
use BaconQrCode\Encoder\QrCode;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function firebase()
    {
        echo '<pre>';
        print_r("hii");
        die;
        $serviceAccount = (new Factory)->withServiceAccount($_SERVER['DOCUMENT_ROOT'] . '/../firebase.json');
        $db = new FirestoreClient([
            'projectId' => 'easytap-c9657',
        ]);
        $usersRef = $db->collection('users');
        $query = $usersRef->where('address', '=', '');
        $snapshot = $query->documents();

        foreach ($snapshot as $document) {
            if ($document->exists()) {

                if (isset($document->data()['createdOn'])) {
                    $old_date = explode(' ', $document->data()['createdOn']);
                    $month_number = $this->getMonthNumber($old_date[1]);
                    $new_date = $old_date[3] . '-' . $month_number . '-' . $old_date[2] . ' 00:00:00';
                }

                if (isset($document->data()['email'])) {
                    $find_user = User::where('email', $document->data()['email'])->count();
                    if ($find_user == 0) {
                        $result['status'] = 0;
                        $result['msg'] = "Something went wrong please try again";
                        $qr_code = $this->qr_code_genrate($document->data()['email']);

                        $insertData = new User;
                        if (isset($document->data()['email'])) {
                            $insertData->email = $document->data()['email'];
                        }
                        if (isset($document->data()['name']) && isset($document->data()['surname'])) {
                            $insertData->name = $document->data()['name'] . ' ' . $document->data()['surname'];
                        } else if (isset($document->data()['name'])) {
                            $insertData->name = $document->data()['name'];
                        } else if (isset($document->data()['surname'])) {
                            $insertData->name = $document->data()['surname'];
                        }
                        if (isset($document->data()['profile_img'])) {
                            $insertData->profile_pic = $document->data()['profile_img'];
                        }
                        if (isset($document->data()['profile_logo'])) {
                            $insertData->personal_logo = $document->data()['profile_logo'];
                        }
                        if (isset($document->data()['mobile'])) {
                            $insertData->phone = $document->data()['mobile'];
                        }
                        if (isset($document->data()['address'])) {
                            $insertData->location = $document->data()['address'];
                        }
                        if (isset($document->data()['createdOn'])) {
                            $insertData->created_at = $new_date;
                        }
                        if (isset($document->data()['dob'])) {
                            $old_dob_date = explode('/', $document->data()['dob']);
                            if ($old_dob_date[0] != '' && $old_dob_date[0] != null && isset($old_dob_date[1])) {
                                $new_dob_date = $old_dob_date[2] . '-' . $old_dob_date[1] . '-' . $old_dob_date[0] . ' 00:00:00';
                                $insertData->dob = $new_dob_date;
                            }
                        }
                        if (isset($document->data()['bio'])) {
                            $insertData->personal_info = $document->data()['bio'];
                        }
                        if (isset($qr_code)) {
                            $insertData->username = $qr_code;
                        }
                        $insertData->status = 1;
                        $insertData->is_block = 0;
                        $insertData->type = 1;
                        $insertData->save();
                        $insert_id = $insertData->id;
                        if ($insert_id > 0) {

                        if (isset($document->data()['social'])) {
                            foreach ($document->data()['social'] as $social) {

                                $insertsocialData = new UserSocialLink;
                                if (isset($insert_id) && isset($social['value']) || isset($social['title'])) {
                                    $insertsocialData->user_id = $insert_id;
                                }
                                if (isset($social['title'])) {
                                    $typedata = $this->selectType($social['title']);
                                    $insertsocialData->social_key = $typedata['title'];
                                }
                                if (isset($typedata['type'])) {
                                    $insertsocialData->type = $typedata['type'];
                                }
                                if (isset($social['value'])) {
                                    $insertsocialData->social_value = $social['value'];
                                }
                                if (isset($document->data()['createdOn']) && isset($insert_id) && isset($social['value']) || isset($social['title'])) {
                                    $insertsocialData->created_at = $new_date;
                                }
                                if (isset($insert_id) && isset($social['value']) || isset($social['title'])) {
                                    $insertsocialData->status = 1;
                                    $insertsocialData->save();
                                }
                            }
                        }

                        if (isset($document->data()['redirect'])) {
                            foreach ($document->data()['redirect'] as $redirect) {
                                $insertredirectData = new UserSocialLink;
                                if (isset($insert_id) && isset($redirect['title']) || isset($redirect['url']) && isset($redirect['url']) != null && isset($redirect['url']) != '') {
                                    $insertredirectData->user_id = $insert_id;
                                }
                                if (isset($redirect['title'])) {
                                    $typedata = $this->selectType($redirect['title']);
                                    $insertredirectData->social_key = $typedata['title'];
                                }
                                if (isset($type)) {
                                    $insertredirectData->type = $typedata['type'];
                                }
                                if (isset($redirect['url']) && isset($redirect['url']) != null && isset($redirect['url']) != '') {
                                    $insertredirectData->social_value = $redirect['url'];
                                }
                                if (isset($document->data()['createdOn']) && isset($insert_id) && isset($redirect['title']) || isset($redirect['url']) && isset($redirect['url']) != null && isset($redirect['url']) != '') {
                                    $insertredirectData->created_at = $new_date;
                                }
                                if (isset($insert_id) && isset($redirect['title']) || isset($redirect['url']) && isset($redirect['url']) != null && isset($redirect['url']) != '') {
                                    $insertredirectData->status = 1;
                                    $insertredirectData->save();
                                }
                            }
                        }

                        if (isset($document->data()['green_cards'])) {
                            foreach ($document->data()['green_cards'] as $green_cards) {
                                $insertgreen_cardsData = new UserSocialLink;
                                if (isset($insert_id) && isset($green_cards['title']) && isset($green_cards['url'])) {
                                    $insertgreen_cardsData->user_id = $insert_id;
                                }
                                if (isset($green_cards['title'])) {
                                    $typedata = $this->selectType($green_cards['title']);
                                    $insertgreen_cardsData->social_key = $typedata['title'];
                                }
                                if (isset($type)) {
                                    $insertgreen_cardsData->type = $typedata['type'];
                                }
                                if (isset($green_cards['url']) && isset($green_cards['title'])) {
                                    $insertgreen_cardsData->social_value = $green_cards['url'];
                                }
                                if (isset($document->data()['createdOn']) && isset($insert_id) && isset($green_cards['title']) && isset($green_cards['url'])) {
                                    $insertgreen_cardsData->created_at = $new_date;
                                }
                                if (isset($insert_id) && isset($green_cards['title']) && isset($green_cards['url'])) {
                                    $insertgreen_cardsData->status = 1;
                                    $insertgreen_cardsData->save();
                                }
                            }
                        }

                        if (isset($document->data()['files'])) {
                            foreach ($document->data()['files'] as $files) {
                                $insertfilesData = new UserSocialLink;
                                if (isset($insert_id) && isset($files['title']) && isset($files['url']) && isset($files['url']) != null && isset($files['url']) != '') {
                                    $insertfilesData->user_id = $insert_id;
                                }
                                if (isset($files['title'])) {
                                    $typedata = $this->selectType($files['title']);
                                    $insertfilesData->social_key = $typedata['title'];
                                }
                                if (isset($type)) {
                                    $insertfilesData->type = $typedata['type'];
                                }
                                if (isset($files['url'])  && isset($files['url']) != null && isset($files['url']) != '') {
                                    $insertfilesData->social_value = $files['url'];
                                }
                                if (isset($document->data()['createdOn']) && isset($insert_id) && isset($files['title']) || isset($files['url']) && isset($files['url']) != null && isset($files['url']) != '') {
                                    $insertfilesData->created_at = $new_date;
                                }
                                if (isset($insert_id) && isset($files['title']) && isset($files['url']) && isset($files['url']) != null && isset($files['url']) != '') {
                                    $insertfilesData->status = 1;
                                    $insertfilesData->save();
                                }
                            }
                        }

                        $result['status'] = 1;
                        $result['msg'] = "User Data created Successfully";
                        }
                    } else {
                        echo "The email address you entered has already been registered.";
                    }
                }
            }
        }
        
    }


    function qr_code_genrate($email)
    {       
        $username = strstr($email, '@', true);
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
            if (!is_dir(QR_CODE_PATH)) {
                $path = public_path(QR_CODE_PATH);
                mkdir($path, null, true);
            }

            \QrCode::size(500)
                ->margin(3)
                // ->format('png')
                // ->merge(public_path('uploads'.DIRECTORY_SEPARATOR.'Qrcode'.DIRECTORY_SEPARATOR.'logo.png'), 0.2, true)
                ->generate($url, public_path(QR_CODE_PATH . $finalstring . '.svg'));
            // die;
            return $finalstring;
        }   
    }

    function selectType($title)
    {
        $linAry = '';
        $contactInfo = '' . TEXT . ',' . CONTACT_CARD . ',' . EMAIL . ',' . WHATS_APP . ',' . ADDRESS . ',' . FACE_TIME . ',' . CALL . ',' . VIBER . ',' . WE_CHAT . ',' . PHONE . ',';
        $socialMedia = '' . INSTAGRAM . ',' . LINKEDIN . ',' . SNAPCHAT . ',' . TIK_TOK . ',' . FACEBOOK . ',' . TWITTER . ',' . YOU_TUBE . ',' . PINTEREST . ',' . MEDIUM . ',' . LIKEE . ',' . MESSENGER . ',' . TINDER . ',' . HOUSE_PARTY . ',' . PERISCOPE . ',' .  DISCORD . ',' . ONLY_FANS . ',' . RADDIT . ',' . TRILLER . ',' . UNTAPPD . ',' . KIK . ',' . HANGOUTS . ',' . MEETME . ',' . LINE . ',' . TWITCH . ',' . TELEGRAM . ',' . STRIPE . ',' . TUMBLR . ',';
        $music = '' . SPOTIFY . ',' . APPLE_MUSIC . ',' . SOUND_CLOUD . ',' . CLUB_HOUSE . ',';
        $payments = '' . CASH_APP . ',' . VENMO . ',' . PAY_PAL . ',' . CRYPTO_WALLET . ',';
        $business = '' . WEBSITE . ',' . ETSY . ',' . APP_LINK . ',' . YELP . ',' . CUSTOM_LINK . ',' . LINK_TREE . ',' . GOOGLE_REVIEWS . ',' . TRUST_PILOT . ',' . TRIP_ADVISOR . ',';
        $meetings = '' . ZOOM . ',' . SKYPE . ',' . MICROSOFT_TEAMS . ',' . CALENDLY . ',' . BOOKSY . ',';
        $others = '' . PODCASTS . ',' . SQUARE . ',' . IMAGE_CAROUSELS . ',' . AUDIO_FILE . ',' . DOCUMENTS_FILE . ',' . OPENSEA . ',' . MEDIA_KITS . ',' . IMAGE . ',' . VIDEO . ',' . GREEN_PASS . ',' . VSCO . ',' . VIMEO . '';

        $linAry .=   $contactInfo;
        $linAry .=   $socialMedia;
        $linAry .=   $music;
        $linAry .=   $payments;
        $linAry .=   $business;
        $linAry .=   $meetings;
        $linAry .=   $others;
        $linAry = explode(',', $linAry);
        $sameVal = '';

        foreach ($linAry as $key => $value) {
            $title = strtolower($title);
            if (str_contains(str_replace(' ', '', $title), str_replace('_', '', $value))) {
                $sameVal = $value;
            }
        }
        // $contactInfo = 'text,contact_card,email,whats_app,address,face_time,call,viber,we_chat';
        $type = 0;

        if (str_contains($contactInfo, $sameVal)) {
            $type = 1;
        } else if (str_contains($socialMedia, $sameVal)) {
            $type = 2;
        } else if (str_contains($music, $sameVal)) {
            $type = 3;
        } else if (str_contains($payments, $sameVal)) {
            $type = 4;
        } else if (str_contains($business, $sameVal)) {
            $type = 5;
        } else if (str_contains($meetings, $sameVal)) {
            $type = 6;
        } else if (str_contains($others, $sameVal)) {
            $type = 7;
        }

        $data = array();
        $data['type'] = $type;
        $data['title'] = $sameVal;
        return $data;
    }

    function getMonthNumber($monthStr)
    {
        $m = trim($monthStr);
        switch ($m) {
            case "Jan":
                $m = "01";
                break;
            case "Feb":
                $m = "02";
                break;
            case "Mar":
                $m = "03";
                break;
            case "Apr":
                $m = "04";
                break;
            case "May":
                $m = "05";
                break;
            case "Jun":
                $m = "06";
                break;
            case "Jul":
                $m = "07";
                break;
            case "Aug":
                $m = "08";
                break;
            case "Sep":
                $m = "09";
                break;
            case "Oct":
                $m = "10";
                break;
            case "Nov":
                $m = "11";
                break;
            case "Dec":
                $m = "12";
                break;
            default:
                $m = false;
                break;
        }
        return $m;
    }
}
