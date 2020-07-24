<?php

namespace Cloudcms\Test;

abstract class AbstractWithRepositoryTest extends AbstractTest
{
    protected static $repository;
    protected $branch;

    /**
     * @beforeClass
     */
    public static function setupRepository()
    {
        self::$repository = self::$platform->createRepository(array("title" => "PHP Driver Test"));
    }

    /**
     * @before
     */
    public function setupBranch()
    {
        $this->branch = self::$repository->readBranch("master");
    }

    /**
     * @afterClass
     */
    public static function teardownRepository()
    {
        self::$repository->delete();
    }
}