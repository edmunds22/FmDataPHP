<?php

namespace edmunds22\fmdataphp;

/**
 * Class ehs
 * @package edmunds22\fmdataphp
 */
class fmdataphp
{

    private $host;
    private $username;
    private $password;
    private $authToken = null;

    public function __construct($host, $username, $password)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }


    public function auth()
    {

        $additionalHeaders = '';
        $ch = curl_init($this->host . 'sessions');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $additionalHeaders));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $json_token = json_decode($result, true);

        $token_received = $json_token['response']['token'];

        if ($token_received) {
            $this->authToken = $token_received;
            return true;
        }

        $this->authToken = $token_received;

        throw new \Exception('Authenticaion or data api connection error.');

    }


    public function insert($layout, $payload)
    {

        $additionalHeaders = "Authorization: Bearer " . $this->authToken;

        $ch = curl_init($this->host . 'layouts/' . $layout . '/records');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $additionalHeaders));
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        $json_data = json_decode($result);

        return json_decode($json_data);

    }


//

    public function get($layout, $payload = null)
    {

        $additionalHeaders = "Authorization: Bearer " . $this->authToken;
        $url = $this->host . 'layouts/' . $layout . '/records';
        if ($payload) {
            $url = $this->host . 'layouts/' . $layout . '/_find';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $additionalHeaders));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($payload) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        $json_data = json_decode($result);

        return $json_data;

    }

}