<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Http\CurlClient;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;

class VideoChatController extends Controller
{

//    ------------------  Genrate Acces Token API --------------------------
    public function generateAcessToken(Request $request)
    {

        
        $identity = uniqid();
        $token = new AccessToken(config('app.TWillio_ACCOUNT_SID'), config('app.TWILLIO_API_KEY'), config('app.TWILLIO_API_SECRET'), 3600, $identity);
        $grant = new VideoGrant();
        $grant->setRoom($request->setRoomMsg);
        $token->addGrant($grant);

        return ['result_status' => 0, 'token' => $token->toJWT()];
    }

//    ------------------ call Notifiy Registration  --------------------------

    public function callNotifyRegistration(Request $request)
    {

        $twilio = new Client(config('app.TWillio_ACCOUNT_SID'), config('app.TWILLIO_AUTH_TOKEN'));
        $curlOptions = [CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false];
        $twilio->setHttpClient(new CurlClient($curlOptions));
        $binding = $twilio->notify->v1->services(config('app.TWILLIO_SERVICE_SID'),'CRb51867cbd4286cbba05e8daef8d036a1')
            ->bindings
            ->create(
                $request->identity,
                $request->bindingType,
                $request->address,
            )->toArray();

        return $binding;
    }

 //    ------------------ Start Call Notification API  --------------------------

    public function startCallNotification(Request $request)
    {

        $twilio = new Client(config('app.TWillio_ACCOUNT_SID'), config('app.TWILLIO_AUTH_TOKEN'));
        $curlOptions = [CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false];
        $twilio->setHttpClient(new CurlClient($curlOptions));

        $notification = $twilio->notify->v1->services(config('app.TWILLIO_SERVICE_SID'),'CRb51867cbd4286cbba05e8daef8d036a1')->notifications->create(
            [
                "body" => $request->body,
                "identity" => $request->identity,
            ]
        )->toArray();

        return $notification;
    }
}
