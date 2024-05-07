<?php

namespace App\Classes;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;

class OnepayLKPayment{

    protected string $config_file;
    protected string $app_id;
    protected string $hash_salt;
    protected string $app_token;
    protected string $url;

    private string $json_encode_data;

    public array $result;

    public string $amount;
    public string $reference;
    public string $customer_first_name;
    public string $customer_last_name;
    public string $customer_phone_number;
    public string $customer_email;
    public string $transaction_redirect_url;
    public string $additional_data = "";

    public function __construct(string $config_file = "onepay-lk-payment")
    {
        $this->app_id = config($config_file.'.app_id');
        $this->hash_salt = config($config_file.'.hash_salt');
        $this->app_token = config($config_file.'.app_token');
        $this->url = config($config_file.'.app_payment_gateway_url');
    }

    public function initPayment(){

        $onepay_args = array(    
            "amount" => floatval($this->amount),
            "app_id"=> $this->app_id,
            "reference" => "{$this->reference}",
            "customer_first_name" => $this->customer_first_name,
            "customer_last_name"=> $this->customer_last_name,
            "customer_phone_number" => $this->customer_phone_number,
            "customer_email" => $this->customer_email,
            "transaction_redirect_url" => $this->transaction_redirect_url,
            "additional_data" => $this->additional_data,
        );

        $this->json_encode_data = json_encode($onepay_args,JSON_UNESCAPED_SLASHES);

        $data_json = $this->json_encode_data."".$this->hash_salt;

        $hash_result = hash('sha256',$data_json);

        $this->url .= $hash_result;

    }

    public function sendPaymentRequest(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $this->json_encode_data,
            CURLOPT_HTTPHEADER => array(
                'Authorization:'."".$this->app_token,
                'Content-Type:application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $this->result = json_decode($response, true);

        /* sample response 
        {
            "status": 1000,
            "message": "success",
            "data": {
                "ipg_transaction_id": "7CJ7118C0E2175226106A",
                "amount": {
                    "gross_amount": 202.0,
                    "discount": 0,
                    "handling_fee": 0,
                    "net_amount": 202.0,
                    "currency": "LKR"
                },
                "gateway": {
                    "redirect_url": "https://gateway-v2.onepay.lk/redirect/LS341187BA4F08DF37ABE/7CJ7118C0E2175226106A/86f8717cce85bc69affa9d4950bce9c02d30c5643e34219b53bacbf0a681f263"
                }
            }
        }
        */
    }

    public function processPayment(){

        if (isset($this->result['data']['gateway']['redirect_url'])) {

            $redirect_url = $this->result['data']['gateway']['redirect_url'];
            
            return redirect($redirect_url);

        } else {

            return $this->result['message'];

        }

    }

}