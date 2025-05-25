<?php
class ApiClient
{
    public function post($url, $data, $cookies = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($cookies) curl_setopt($ch, CURLOPT_COOKIE, http_build_query($cookies, '', '; '));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function get($url, $params = [], $cookies = [])
    {
        $url .= $params ? '?' . http_build_query($params) : '';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($cookies) curl_setopt($ch, CURLOPT_COOKIE, http_build_query($cookies, '', '; '));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
?>