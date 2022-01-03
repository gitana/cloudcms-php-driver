<?php

namespace CloudCMS\Test;

use CloudCMS\Node;
use CloudCMS\Directionality;

final class TraverseTest extends AbstractWithRepositoryTest
{
    private function createFile($branch, $parent, $filename, $isFolder)
    {
        $node = $branch->createNode(array("title" => $filename));
        $node->addFeature("f:filename", array("filename" => $filename));
        if ($isFolder)
        {
            $node->addFeature("f:container", array());
        }

        $parent->associate($node, "a:child", Directionality::DIRECTED);
        return $node;
    }

    public function testTraverse()
    {
        // folder1
        // file1
        // folder1/folder2
        // folder1/file2
        // folder1/file4
        // folder1/folder2/file3
        // folder1/folder2/file5

        $rootNode = $this->branch->rootNode();
        $folder1 = $this->createFile($this->branch, $rootNode, "folder1", true);
        $file1 = $this->createFile($this->branch, $rootNode, "file1", false);
        $folder2 = $this->createFile($this->branch, $folder1, "folder2", true);
        $file2 = $this->createFile($this->branch, $folder1, "file2", false);
        $file3 = $this->createFile($this->branch, $folder2, "file3", false);
        $file4 = $this->createFile($this->branch, $folder1, "file4", false);
        $file5 = $this->createFile($this->branch, $folder2, "file5", false);

        // test path resolves
        $path = $file5->resolvePath();
        $this->assertEquals('/folder1/folder2/file5', $path);

        $paths = $file5->resolvePaths();
        $this->assertGreaterThan(0, sizeof($paths));

        $traverse = [
            "depth" => 1,
            "filter" => "ALL_BUT_START_NODE",
            "associations" => [
                "a:child" => "ANY"
            ]
        ];

        sleep(5);

        $results =$rootNode->traverse($traverse);
        $this->assertEquals(2, sizeof($results->nodes));
        $this->assertEquals(2, sizeof($results->associations));
    }

}