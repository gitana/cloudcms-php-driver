<?php

namespace CloudCMS\Test;

use CloudCMS\Repository;

final class RepositoryTest extends AbstractTest
{
    public function testRepositories()
    {
        $repositories = self::$platform->listRepositories();
        $this->assertInternalType("array", $repositories);
        $this->assertGreaterThan(0, sizeof($repositories));
        foreach($repositories as $rep)
        {
            $this->assertInstanceOf(Repository::class, $rep);
        }

        $repository = self::$platform->createRepository();

        $this->assertInstanceOf(Repository::class, $repository);
        $this->assertEquals("/repositories/" . $repository->id, $repository->uri());

        $repositoryRead = self::$platform->readRepository($repository->id);
        $this->assertEquals($repository, $repositoryRead);

        $repository->delete();
        $repositoryRead = self::$platform->readRepository($repository->id);
        $this->assertNull($repositoryRead);

        $repository = self::$platform->readRepository("I'm not real");
        $this->assertNull($repository);
    }
}