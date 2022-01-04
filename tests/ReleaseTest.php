<?php

namespace CloudCMS\Test;

final class ReleaseTest extends AbstractWithRepositoryTest
{
    public function testReleases()
    {
        $releaseJob = self::$repository->startCreateRelease(array("title"=>"blah"));
        $releaseJob->waitForCompletion();

        $releases = self::$repository->listReleases();
        $this->assertEquals(1, sizeof($releases));
        $firstRelease = $releases[0];

        $release = self::$repository->readRelease($firstRelease->id);
        $this->assertNotNull($release);

        $queriedReleases = self::$repository->queryReleases(array());
        $this->assertEquals(1, sizeof($queriedReleases));
    }
}