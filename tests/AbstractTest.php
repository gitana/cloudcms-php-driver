<?php

namespace Cloudcms\Test;

use PHPUnit\Framework\TestCase;
use CloudCMS\CloudCMS;

abstract class AbstractTest extends TestCase
{
    protected static $client;
    protected static $platform;

    /**
     * @beforeClass
     */
    public static function setupCloudCMS()
    {
        self::$client = new CloudCMS();

        $config_string = file_get_contents("gitana.json");
        $config = json_decode($config_string, true);

        // Authenticate with admin account
        $config["username"] = "admin";
        $config["password"] = "admin";

        self::$platform = self::$client->connect($config);
    }
}