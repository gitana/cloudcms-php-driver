<?php

namespace CloudCMS\Test;

use CloudCMS\Branch;

final class BranchTest extends AbstractWithRepositoryTest
{
    public function testBranches()
    {
        $branches = self::$repository->listBranches();
        $this->assertInternalType("array", $branches);
        $this->assertGreaterThan(0, sizeof($branches));
        foreach($branches as $br)
        {
            $this->assertInstanceOf(Branch::class, $br);
        }

        $branch = self::$repository->readBranch("master");
        $this->assertInstanceOf(Branch::class, $branch);
        $this->assertEquals("/repositories/" . self::$repository->id . "/branches/" . $branch->id, $branch->uri());
        $this->assertTrue($branch->isMaster());

        $branch = self::$repository->readBranch("I'm not real");
        $this->assertNull($branch);
    }


}