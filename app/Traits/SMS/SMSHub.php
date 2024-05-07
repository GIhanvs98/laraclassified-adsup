<?php

namespace App\Traits\SMS;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Exception;

trait SMSHub
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

        if (!$apiToken || !$apiUrl) {
            throw new Exception('SMSHub sms gateway configuration is incomplete.');
        }

        $response = Http::withHeaders([
            'Authorization' => $apiToken,
            'Content-Type' => 'application/json'
        ])->post($apiUrl, [
                    'message' => $this->message,
                    'phoneNumber' => $phone,
                ]);

        return $response;
    }
}
