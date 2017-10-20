<?php

class Bca
{
    private static $main_url = 'https://sandbox.bca.co.id'; // Change When Your Apps is Live
    private static $client_id = 'fa476214-c010-4930-8908-9a42a5ccc463'; // Fill With Your Client ID
    private static $client_secret = '8bb6547a-2324-44c8-88ac-2d2a50d1d80a'; // Fill With Your Client Secret ID
    private static $api_key = '7b66f4ae-3df7-46ff-a470-17657447c0af'; // Fill With Your API Key
    private static $api_secret = '0abe0bc4-6415-457e-9af3-bdf8a6c1f2bc'; // Fill With Your API Secret Key
    private static $access_token = null;
    private static $signature = null;
    private static $timestamp = null;
    private static $corporate_id = 'BCAAPI2016'; // Fill With Your Corporate ID. BCAAPI2016 is Sandbox ID
    private static $account_number = '0201245680'; // Fill With Your Account Number. 0201245680 is Sandbox Account

    private function getToken()
    {
        $path = '/api/oauth/token';
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . base64_encode(self::$client_id . ':' . self::$client_secret));
        $data = array(
            'grant_type' => 'client_credentials',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$main_url . $path);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore Verify SSL Certificate
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => http_build_query($data),
        ]);
        $output = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($output, true);
        self::$access_token = $result['access_token'];
        // var_dump(self::$access_token);
    }

    private function parseSignature($res)
    {
        $explode_response = explode(',', $res);
        $explode_response_1 = explode(':', $explode_response[8]);
        self::$signature = trim($explode_response_1[1]);
    }

    private function parseTimestamp($res)
    {
        $explode_response = explode(',', $res);
        $explode_response_1 = explode('Timestamp: ', $explode_response[3]);
        self::$timestamp = trim($explode_response_1[1]);
    }

    private function getSignature($url, $method, $data)
    {
        $path = '/utilities/signature';
        $timestamp = date(DateTime::ISO8601);
        $timestamp = str_replace('+', '.000+', $timestamp);
        $timestamp = substr($timestamp, 0, (strlen($timestamp) - 2));
        $timestamp .= ':00';
        $url_encode = $url;
        $headers = array(
            'Timestamp: ' . $timestamp,
            'URI: ' . $url_encode,
            'AccessToken: ' . self::$access_token,
            'APISecret: ' . self::$api_secret,
            'HTTPMethod: ' . $method,
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$main_url . $path);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore Verify SSL Certificate
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);
        $output = curl_exec($ch);
        curl_close($ch);
        $this->parseSignature($output);
        $this->parseTimestamp($output);
        var_dump(self::$signature);
    }

    public function BalanceInformation()
    {
        $path = '/banking/v2/corporates/' . self::$corporate_id . '/accounts/' . self::$account_number;
        $method = 'GET';
        $data = [];

        $this->getToken();
        $this->getSignature($path, $method, $data);

        $headers = [
            'Authorization:' . self::$access_token,
        ];

    }
}
