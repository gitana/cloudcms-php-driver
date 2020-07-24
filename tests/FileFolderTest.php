<?php

namespace CloudCMS\Test;

use CloudCMS\Node;
use CloudCMS\Direction;
use CloudCMS\Directionality;

final class FileFolderTest extends AbstractWithRepositoryTest
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

    public function testFileFolder()
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

        $tree = $rootNode->fileFolderTree();
        $this->assertEquals(2, sizeof($tree["children"]));
        
        $child = $tree["children"][0];
        $this->assertNotNull($child["filename"]);
        $this->assertNotNull($child["label"]);
        $this->assertNotNull($child["path"]);
        $this->assertNotNull($child["typeQName"]);
        $this->assertNotNull($child["qname"]);

        $folder2Children = $folder2->listChildren();
        $this->assertEquals(2, sizeof($folder2Children));

        $folder2Relatives = $folder2->listRelatives("a:child", Direction::ANY);
        $this->assertEquals(3, sizeof($folder2Relatives));

        $folder2RelativesQueried = $folder2->queryRelatives("a:child", Direction::ANY, array("title" => "file3"));
        $this->assertEquals(1, sizeof($folder2RelativesQueried));
    }
}