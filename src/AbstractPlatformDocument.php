<?php

namespace CloudCMS;

abstract class AbstractPlatformDocument extends AbstractDocument
{
    public $platform;
    public $platformId;

    public function __construct($platform, $obj)
    {
        parent::__construct($platform->client, $obj);

        $this->platform = $platform;
        $this->platformId = $platform->id;
    }
}