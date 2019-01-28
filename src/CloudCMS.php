<?php

namespace CloudCMS;

use CloudCMS\Platform;
use GuzzleHttp\Psr7;

class CloudCMS
{
    private $provider;
    private $config;
    private $token;

    private static $requiredConfig = ["clientKey", "clientSecret", "username", "password", "baseURL"];

    public function __construct()
    {

    }

    public function connect($config)
    {
        // Ensure required config options are set
        $configKeys = array_keys($config);
        $missingKeys = array_diff(self::$requiredConfig, $configKeys);
        if (!empty($missingKeys))
        {
            throw new InvalidArgumentException("Missing required config keys: " . implode(", ", $missingKeys));
        }
        $this->baseURL = $config["baseURL"];

        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            "clientId" => $config["clientKey"],
            "clientSecret" => $config["clientSecret"],
            "urlResourceOwnerDetails" => $this->baseURL . "/oauth/token",
            "urlAuthorize" => $this->baseURL . "/oauth/token",
            "urlAccessToken" => $this->baseURL . "/oauth/token"
        ]);
        
        $this->token = $this->provider->getAccessToken("password", [
            "username" => $config["username"],
            "password" => $config["password"]
        ]);

        return $this->readPlatform();
    }

    public function request($method, $uri, $params = array(), $data = array())
    {
        // Refresh token if expired
        if ($this->token->hasExpired())
        {
            $newToken = $this->provider->getAccessToken("refresh_token", [
                "refresh_token" => $this->token->getRefreshToken()
            ]);
            $this->token = $newToken;
        }

        // Add "full" to params if not present
        if (!isset($params["full"]))
        {
            $params["full"] = true;
        }

        // Change all boolean params to strings so they are properly interpreted by the API
        foreach($params as &$value)
        {
            if (is_bool($value))
            {
                $value = ($value) ? "true" : "false";
            }
        }

        $url = $this->baseURL . $uri . "?" . Psr7\build_query($params);
        $request = $this->provider->getAuthenticatedRequest($method, $url, $this->token, [
            "body" => json_encode($data, JSON_FORCE_OBJECT)
        ]);
        $response = $this->provider->getResponse($request);
        
        return json_decode($response->getBody(), true);
    }

    public function get($uri, $params = array())
    {
        return $this->request("GET", $uri, $params);
    }

    public function post($uri, $params = array(), $data = array())
    {
        return $this->request("POST", $uri, $params, $data);
    }

    public function put($uri, $params = array(), $data = array())
    {
        return $this->request("PUT", $uri, $params, $data);
    }

    public function delete($uri, $params = array())
    {
        return $this->request("DELETE", $uri, $params);
    }
    
    public function readPlatform()
    {
        $res = $this->get("");
        return new Platform($this, $res);
    }
}