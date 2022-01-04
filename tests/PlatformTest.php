<?php

namespace CloudCMS\Test;

final class PlatformTest extends AbstractTest
{
    public function testReadPlatform()
    {
        $this->assertEquals("Root Platform", self::$platform->data['title']);
        $this->assertEquals("", self::$platform->uri());
    }
}