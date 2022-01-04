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

        $queriedBranches = self::$repository->queryBranches(array());
        $this->assertGreaterThan(0, sizeof($queriedBranches));

        $branch = self::$repository->readBranch("master");
        $this->assertInstanceOf(Branch::class, $branch);
        $this->assertEquals("/repositories/" . self::$repository->id . "/branches/" . $branch->id, $branch->uri());
        $this->assertTrue($branch->isMaster());

        $fakeBranch = self::$repository->readBranch("I'm not real");
        $this->assertNull($fakeBranch);

        // Test reset
        $originalChangeset = $branch->data["tip"];
        $node = $branch->createNode();
        $branch->reload();

        $newChangeset = $branch->data["tip"];
        $this->assertNotEquals($originalChangeset, $newChangeset);

        $resetJob = $branch->startReset($originalChangeset);
        $resetJob->waitForCompletion();
        $branch->reload();
        $this->assertEquals($originalChangeset, $branch->data["tip"]);
        $node2 = $branch->readNode($node->id);
        $this->assertNull($node2);
    }


}