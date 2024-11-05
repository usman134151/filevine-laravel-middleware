<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use App\Models\UserAPIList;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RoadProofResponse;
use App\Models\FilevineWebhookLog;
use App\Http\Controllers\Controller;

class RoadProofController extends Controller
{

    public function roadProofWebHook(Request $request, $id)
    {


        $data = RoadProofResponse::where('uuid', $id)->with('user_api_list')->first();
        $item_id = $data->itemId;
        $project_id = $data->project_id;
        $user = $data->user_api_list;
        $filevine_URL = $user->filevine_url;

        $x_fv_timestamp = gmdate('Y-m-d\TH:i:s.') . sprintf('%03d', (int)(microtime(true) * 1000) % 1000) . 'Z';

        $apiKey = $user->filevine_API_key;
        $apiSecret = $user->filevine_API_secret;
        $timestamp = $x_fv_timestamp;

        $access_keys = $this->getAccessTokens($filevine_URL, $apiKey, $timestamp, $apiSecret);

        $access_token = $access_keys[0];
        $session_id = $access_keys[1];

        $UserId = null;
        $OrgId = null;

        $webhook_logs = FilevineWebhookLog::pluck('payload');

        foreach ($webhook_logs as $log) {
            $webhook_item = json_decode($log);

            if (isset($webhook_item->Other->ItemId) && $webhook_item->Other->ItemId == $item_id) {

                $UserId = $webhook_item->UserId;
                $OrgId = $webhook_item->OrgId;

                break;
            }
        }

        $client = new Client();

        $url = $filevine_URL . "core/projects/" . $project_id .
            "/collections/roadProof/" . $item_id;


        $response = $client->request('PATCH', $url, [
            'json' => [
                "itemId" => [
                    "native" => $item_id,
                    "partner" => null
                ],
                "dataObject" => [
                    "status" => $request->status,
                    "urls" => [$request->video_url_1, $request->video_url_2, $request->video_url_3],
                ]
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
                'x-fv-orgid' => $UserId,
                'x-fv-userid' => $OrgId,
                'x-fv-sessionid' => $session_id,
            ],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body);
        return response()->json($data, 200);
    }


    public function getAccessTokens($BaseURL, $API_Key, $Timestamp, $API_Secret)
    {

        $hash_api = [
            $API_Key,
            $Timestamp,
            $API_Secret
        ];
        $data = join('/', $hash_api);
        $apiHash = md5($data);
        $url = $BaseURL . "session";

        $client = new Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                ''
            ],
            'json' => [
                'mode' => 'key',
                'apiKey' => $API_Key,
                'apiHash' => $apiHash,
                'apiTimestamp' => $Timestamp,
            ],
        ]);
        if ($response) {
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            $access_token = $responseData['accessToken'];
            $session_id = $responseData['refreshToken'];
            return [$access_token, $session_id];
        } else {
            return response()->json([
                'error' => 'Failed to create/refresh session',
            ], 500);
        }
    }
}
