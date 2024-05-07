<?php

namespace App\Traits\SMS;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Exception;

trait Ozonedesk
{
    /**
     * Send the api request to send the message through sms gateway.
     * @param string $phone
     * @throws Exception
     * @return Response|null
     */
    public function send(string $phone): Response|null
    {
        $defaultProvider = config('sms.default');

        if (!$defaultProvider) {
            throw new Exception('Select the default sms gateway.');
        }

        $apiToken = config("sms.providers.{$defaultProvider}.api-token");
        $apiUrl = config("sms.providers.{$defaultProvider}.api-url");

        $userId = config("sms.providers.{$defaultProvider}.user-id");
        $senderId = config("sms.providers.{$defaultProvider}.sender-id");

        if (!$apiToken || !$apiUrl || !$userId || !$senderId) {
            throw new Exception('Ozonedesk sms gateway configuration is incomplete.');
        }

        $queryString = http_build_query([
            'api_key' => $apiToken,
            'user_id' => $userId,
            'sender_id' => $senderId,
            'message' => $this->message,
            'to' => $phone,
        ]);

        $response = Http::post("http://send.ozonedesk.com/api/v2/send.php?$queryString");

        return $response;
    }
}