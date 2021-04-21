<?php

use App\Category;
use App\Property;
use App\User;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;

//use FCM;

function explodeByComma($str)
{
    return explode(",", $str);
}

function categories()
{
    return Category::pluck('name', 'id');
}

function providers()
{
    return User::whereType('1')->whereActive(1)->pluck('name', 'id');
}

function properties()
{
    return Property::with(['values' => function ($query) {
        $query->select("id", "name");
    }])->pluck('name', 'id');
}

function explodeByDash($str)
{
    return explode("-", $str);
}

function convertArabicNumbersToEnglish($number)
{
    $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    $num = range(0, 9);
    return (int) str_replace($arabic, $num, $number);
}


function imgPath($folderName)
{
    return '/public/uploads/' . $folderName . '/';
}

function settings()
{
    return \App\Setting::where('id', 1)->first();
}

function validateRules($errors, $rules)
{
    $error_arr = [];
    foreach ($rules as $key => $value) {
        if ($errors->get($key)) {
            array_push($error_arr, array('key' => $key, 'value' => $errors->first($key)));
        }
    }
    return $error_arr;
}

function randNumber($length)
{
    $seed = str_split('0123456789');
    shuffle($seed);
    $rand = '';
    foreach (array_rand($seed, $length) as $k) {
        $rand .= $seed[$k];
    }
    return $rand;
}

function generateApiToken($userId, $length)
{
    $seed = str_split('abcdefghijklmnopqrstuvwxyz' . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789');
    shuffle($seed);
    $rand = '';
    foreach (array_rand($seed, $length) as $k) {
        $rand .= $seed[$k];
    }
    return $userId * $userId . $rand;
}

function UploadBase64Image($base64Str, $prefix, $folderName)
{
    $image = base64_decode($base64Str);
    $image_name = $prefix . '_' . time() . '.png';
    $path = public_path('uploads') . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . $image_name;
    $saved = file_put_contents($path, $image);
    return $saved ? $image_name : null;
}

// function UploadImage($inputRequest, $prefix, $folderNam)
// {
//     $imageName = $prefix . '_' . time() . '.' . $inputRequest->getClientOriginalExtension();
//     // $destinationPath = public_path('/' . $folderNam);
//     // $inputRequest->move($destinationPath, $imageName);
//     // $image_resize = Image::make($.'/'.$imageName);
//     // $image_resize->fit(300, 300);
//     $image = \Image::make($inputRequest)->resize(300, 300);
//     $image->save(public_path('uploads/'.$folderNam.'/' .$imageName));
//     return $imageName ? $imageName : false;
// }

function UploadImage($inputRequest, $prefix, $folderNam)
{
    $imageName = $prefix . '_' . time() . '.' . $inputRequest->getClientOriginalExtension();
    $destinationPath = public_path('/' . $folderNam);
    $inputRequest->move($destinationPath, $imageName);
    return $imageName ? $imageName : false;
}

function UploadImageEdit($inputRequest, $prefix, $folderNam, $oldImage)
{
    if ($oldImage != null && $oldImage != 'default.png') {
        if (file_exists(public_path('/' . $folderNam . '/' . $oldImage))) {
            @unlink(public_path('/' . $folderNam . '/' . $oldImage));
        }
    }
    $imageName = $prefix . '_' . time() . '.' . $inputRequest->getClientOriginalExtension();
    $destinationPath = public_path('/' . $folderNam);
    $inputRequest->move($destinationPath, $imageName);
    return $imageName ? $imageName : false;
}

function notificationShortcutTypes()
{
    return [
        '1' => 'create_join',
        '2' => 'cancel_join',
        '3' => 'accept_join',
        '4' => 'reject_join',
        '5' => 'admin',
        '6' => 'target'
    ];
}

function getNotificationType($typeNum)
{
    $types = notificationShortcutTypes();
    foreach ($types as $key => $value) {
        if ($typeNum == $key) {
            return $value;
        }
    }
}

function genders()
{
    return [
        'M' => 'ذكر',
        'F' => 'أنثى'
    ];
}

function getGenderString($char)
{
    $genders = genders();
    foreach ($genders as $key => $value) {
        if ($char == $key) {
            return $value;
        }
    }
}

function targetGenders()
{
    return [
        'M' => ['M'],
        'F' => ['F'],
        'B' => ['M', 'F']
    ];
}

function getTargetGenderArr($char)
{
    $genders = targetGenders();
    foreach ($genders as $key => $value) {
        if ($char == $key) {
            return $value;
        }
    }
}

function ageIntervals()
{
    return  ['10-20', '20-30', '30-40', '40-50', '50-60', '60-70'];
}

function getAgeIntervalMinMax($interval)
{
    $ageIntervals = ageIntervals();
    foreach ($ageIntervals as $value) {
        // return ['$interval' => gettype($interval), '$value' => gettype($value)];
        if ($interval == $value) {
            // return 'eman';
            return explodeByDash($value);
        }
        // else
        // return 'hi';
    }
}

function siteImagesTypes()
{
    return [
        'slider' => '1',
        'app' => '2'
    ];
}

function getSiteImagesTypes($type)
{
    $siteImagesTypes = siteImagesTypes();
    foreach ($siteImagesTypes as $key => $value) {
        if ($type == $key) {
            return $value;
        }
    }
    return false;
}

function usersTypes()
{
    return [
        'admin' => '1',
        'user' => '2',
        'company' => '3'
    ];
}

function getIntUserType($type)
{
    $users = usersTypes();
    foreach ($users as $key => $value) {
        if ($type == $key) {
            return $value;
        }
    }
    return false;
}

function endsWith($string, $finding)
{
    $length = strlen($finding);
    if ($length == 0) {
        return true;
    }
    return (substr($string, -$length) === $finding);
}


function sendNotification($notificationTitle, $notificationBody, $deviceToken)
{
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60 * 20);
    $notificationBuilder = new PayloadNotificationBuilder($notificationTitle);
    $notificationBuilder->setBody($notificationBody)
        ->setSound('default');
    $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['a_data' => 'my_data']);
    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();
    $token = $deviceToken;
    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();
    //return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();
    //return Array (key : oldToken, value : new token - you must change the token in your database )
    $downstreamResponse->tokensToModify();
    //return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();
    // return Array (key:token, value:errror) - in production you should remove from your database the tokens
}

function sendMultiNotification($notificationTitle, $notificationBody, $devicesTokens, $order_id = null)
{
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60 * 20);
    $notificationBuilder = new PayloadNotificationBuilder($notificationTitle);
    $notificationBuilder->setBody($notificationBody)
        ->setSound('default');
    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['order_id' => $order_id]);
    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();
    // You must change it to get your tokens
    $tokens = $devicesTokens;
    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();
    //return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();
    //return Array (key : oldToken, value : new token - you must change the token in your database )
    $downstreamResponse->tokensToModify();
    //return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();
    // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
    $downstreamResponse->tokensWithError();
    return ['success' => $downstreamResponse->numberSuccess(), 'fail' => $downstreamResponse->numberFailure()];
}

function saveNotification($userId, $title, $message, $order_id = null, $type = null)
{
    $created = \App\Notification::create([
        'user_id' => $userId, 'title' => $title,
        'message' => $message, 'order_id' => $order_id, 'type' => $type
    ]);
    return $created;
}

/**
 * calculate the distance between tow places on the earth
 *
 * @param latitude $latitudeFrom
 * @param longitude $longitudeFrom
 * @param latitude $latitudeTo
 * @param longitude $longitudeTo
 * @return double distance in KM
 */
function distanceBetweenTowPlaces($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
{
    $lat1 = deg2rad($latitudeFrom);
    $long1 = deg2rad($longitudeFrom);
    $lat2 = deg2rad($latitudeTo);
    $long2 = deg2rad($longitudeTo);
    //Haversine Formula
    $dlong = $long2 - $long1;
    $dlati = $lat2 - $lat1;
    $val = pow(sin($dlati / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($dlong / 2), 2);
    $res = 2 * asin(sqrt($val));
    $radius = 6367.756;
    return ($res * $radius);
}



// ===============================  MyFatoorah public  function  =========================
function MyFatoorah($userData)
{
    $token = env('MY_FATOORAH_API');
    $basURL = "https://apitest.myfatoorah.com";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$basURL/v2/ExecutePayment",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $userData,
        CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        return $response;
    }
}



####### Check Payment Status ######
function MyFatoorahStatus($PaymentId)
{
    $token = env('MY_FATOORAH_API');
    $basURL = "https://apitest.myfatoorah.com";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$basURL/v2/GetPaymentStatus",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"Key\": \"$PaymentId\",\"KeyType\": \"PaymentId\"}",
        CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        return $response;
    }
}








function sendSMS($jsonObj)
{
    $contextOptions['http'] = array('method' => 'POST', 'header' => 'Content-type: application/json', 'content' => json_encode($jsonObj), 'max_redirects' => 0, 'protocol_version' => 1.0, 'timeout' => 10, 'ignore_errors' => true);
    $contextResouce  = stream_context_create($contextOptions);
    $url = "http://www.alfa-cell.com/api/msgSend.php";
    $arrayResult = file($url, FILE_IGNORE_NEW_LINES, $contextResouce);
    $result = $arrayResult[0];
    return $result;
}