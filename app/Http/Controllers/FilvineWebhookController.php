<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use App\Models\UserAPIList;
use Illuminate\Http\Request;
use App\Models\RoadProofResponse;
use App\Models\FilevineWebhookLog;
use App\Http\Controllers\Controller;
use App\Models\RoadProofRequest;

class FilvineWebhookController extends Controller
{
    public function logWebhook(Request $request)
    {
        $ApiList = UserAPIList::all();

        $payload = $request->all();

        //Webhook Authentication.
        $x_fv_timestamp = $request->header('x-fv-timestamp');
        $x_fv_message_id = $request->header('x-fv-messageid');
        $x_fv_hash = $request->header('x-fv-hash');


        $project_id = $payload['ProjectId'];
        $UserId = $payload['UserId'];
        $OrgId = $payload['OrgId'];
        $filevine_API_key = "";
        $filevine_API_secret = "";
        $filevine_URL = "";
        $found = false;
        $filevine_user_id = null;
        $roadProof_API_key = null;
        $user_name = null;

        foreach ($ApiList as $user) {
            $filevine_API_key = $user->filevine_API_key;
            $filevine_API_secret = $user->filevine_API_secret;
            $filevine_URL = $user->filevine_url;
            $filevine_user_id = $user->id;
            $roadProof_API_key = $user->roadProof_API_key;
            $hash_list = [
                $filevine_API_key,
                $x_fv_timestamp,
                $x_fv_message_id,
                $filevine_API_secret
            ];
            $hash_list = join('/', $hash_list);
            // convert the hashlist to md5 hash
            $hash_final = md5($hash_list);


            if ($hash_final == $x_fv_hash) {
                $found = true;
                $filevine_user_id = $user->id;
                $user_name = $user->client_name;
                $filevine_URL = $user->filevine_url;
                break;
            } else {
                $found = false;
            }
        }

        if (!$found) {
            return response()->json([
                'error' => 'Invalid Webhook Request',
            ], 401);
        }

        $data = json_encode($payload);
        $record = new FilevineWebhookLog();
        $record->payload = $data;
        $record->save();

        $apiKey =  $filevine_API_key;
        $apiSecret = $filevine_API_secret;
        $timestamp = $x_fv_timestamp;

        $access_keys = $this->getAccessTokens($filevine_URL, $apiKey, $timestamp, $apiSecret);

        $access_token = $access_keys[0];
        $session_id = $access_keys[1];


        // Get Filvine RoadProof Data

        $url = $filevine_URL . "core/projects/" . $project_id . "/collections/roadProof";
        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
                'x-fv-orgid' => $OrgId,
                'x-fv-userid' => $UserId,
                'x-fv-sessionid' => $session_id,
            ],
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body);


        $logPath = storage_path('main.log');
        $logData = json_encode($data);
        file_put_contents($logPath, $logData . PHP_EOL, FILE_APPEND);

        $roadProofApi = new RoadProofRequest();
        $roadProofApi->company_id = $filevine_user_id;
        $roadProofApi->user_id = $UserId;
        $roadProofApi->project_id = $project_id;
        $roadProofApi->org_id = $OrgId;
        $roadProofApi->payload = $logData;
        $roadProofApi->save();

        // Send Filveine Data to RoadProof API

        $last_filvine_webhook_data = FilevineWebhookLog::latest()->first('payload');

        $decode_last_webhook_Item = json_decode($last_filvine_webhook_data->payload, true);

        $last_webhook_projectId = $decode_last_webhook_Item['ProjectId'];

        $last_webhook_itemId = $decode_last_webhook_Item['Other']['ItemId'];


        $last_road_data = RoadProofRequest::latest()->first('payload');

        $decodeRoadProofData = json_decode($last_road_data->payload, true);

        $case_name = null;
        $longitude = null;
        $latitude = null;
        $description = null;
        $location = null;
        $state = null;
        $datetime_occurred_local = null;
        $time = null;
        $timezone = null;
        $uuid = Str::uuid()->toString();

        if (isset($decodeRoadProofData['items']) && is_array($decodeRoadProofData['items'])) {
            foreach ($decodeRoadProofData['items'] as $item) {
                if ($item['itemId']['native'] === $last_webhook_itemId) {

                    $case_name = $item['dataObject']['case_name'];
                    $latitude = $item['dataObject']['latitude'];
                    $longitude = $item['dataObject']['longitude'];
                    $location = $item['dataObject']['location'];
                    $description = $item['dataObject']['description'];
                    $state = $item['dataObject']['state'];
                    $datetime_occurred_local = $item['dataObject']['date'];
                    $time = $item['dataObject']['time'];
                    $timezone = $item['dataObject']['timezone'];

                    $responseData = [
                        'case_name' => $case_name,
                        'lat' => $latitude,
                        'lng' => $longitude,
                        'location' => $location,
                        'state' => $state,
                        'description' => $description,
                        'datetime_occurred_local' => $datetime_occurred_local,
                        'timezone' => $timezone,
                        "callback_url" => "https://trusting-severely-terrapin.ngrok-free.app",
                        "client_platform" => "Filevine"
                    ];
                }

                $roadProofUrl = 'https://api.roadproof-staging.com/api/v1/Cases';

                $client = new Client();

                $headers = [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $roadProof_API_key,
                ];

                $body = $responseData;

                $response = $client->request('POST', $roadProofUrl, [
                    'headers' => $headers,
                    'json' => $body,
                ]);

                $body = $response->getBody()->getContents();
                $data = json_decode($body);

                $roadProofResponse = new RoadProofResponse();
                $roadProofResponse->project_id = $last_webhook_projectId;
                $roadProofResponse->user_id = $filevine_user_id;
                $roadProofResponse->uuid = $uuid;
                $roadProofResponse->itemId = $last_webhook_itemId;
                $roadProofResponse->response = json_encode($data);
                $roadProofResponse->save();

                break;
            }
        }

        // Update Filevine with RoadProof Response

        $last_roadproof_response = RoadProofResponse::latest()->first('response');

        $decodeRoadProofResponse = json_decode($last_roadproof_response->response, true);

        $status_roadproof_response = $decodeRoadProofResponse['data']['case_status'];
        $final_status = ($status_roadproof_response == 8) ? 'Processing' : $status_roadproof_response;
        $case_name_roadproof_response = $decodeRoadProofResponse['data']['case_name'];
        $latitude_roadproof_response = $decodeRoadProofResponse['data']['lat'];
        $longitude_roadproof_response = $decodeRoadProofResponse['data']['lng'];
        $location_roadproof_response = $decodeRoadProofResponse['data']['location'];
        $description_roadproof_response = $decodeRoadProofResponse['data']['description'];
        $state_roadproof_response = $decodeRoadProofResponse['data']['state'];
        $datetime_occurred_local_roadproof_response = $decodeRoadProofResponse['data']['datetime_occurred_local'];
        $time_roadproof_response = $decodeRoadProofResponse['data']['datetime_occurred_local'];
        $timezone_roadproof_response = $decodeRoadProofResponse['data']['timezone'];

        $client = new Client();

        $url = $filevine_URL . "core/projects/" . $last_webhook_projectId .
            "/collections/roadProof/" . $last_webhook_itemId;
        $response = $client->request('PATCH', $url, [
            'json' => [
                "itemId" => [
                    "native" => $last_webhook_itemId,
                    "partner" => null
                ],
                "dataObject" => [
                    "case_name" => $case_name_roadproof_response,
                    "date" => $datetime_occurred_local_roadproof_response,
                    "time" => $time_roadproof_response,
                    "timezone" => $timezone_roadproof_response,
                    "longitude" => $longitude_roadproof_response,
                    "latitude" => $latitude_roadproof_response,
                    "location" => $location_roadproof_response,
                    "state" => $state_roadproof_response,
                    "description" => $description_roadproof_response,
                    "status" => $final_status,
                ]
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
                'x-fv-orgid' => $OrgId,
                'x-fv-userid' => $UserId,
                'x-fv-sessionid' => $session_id,
            ],
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body);
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
