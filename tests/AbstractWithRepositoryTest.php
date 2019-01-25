<?php

namespace Cloudcms\Test;

abstract class AbstractWithRepositoryTest extends AbstractTest
{
    protected static $repository;

    /**
     * @beforeClass
     */
    public static function setupRepository()
    {
        self::$repository = self::$platform->createRepository();
    }

    /**
     * @afterClass
     */
    public static function teardownRepository()
    {
        self::$repository->delete();
    }
}