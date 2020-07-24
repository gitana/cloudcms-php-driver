<?php

namespace CloudCMS\Test;

use CloudCMS\Node;
use CloudCMS\Directionality;

final class AttachmentTest extends AbstractWithRepositoryTest
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

    public function testAttachment()
    {
        $node = $this->branch->createNode();
        $cloudcmsImage = fopen("./res/cloudcms.png", "r");

        $node->uploadAttachment($cloudcmsImage, "image/png", "default", "myImage");
        $attachments = $node->listAttachments();
        $attachment = $attachments["default"];
        $this->assertEquals("default", $attachment->id);
        $this->assertEquals("myImage", $attachment->filename);
        $this->assertEquals("image/png", $attachment->contentType);
        $this->assertTrue($attachment->length > 0);
        $this->assertNotNull($attachment->objectId);

        $dl = $attachment->downloadAttachment();
        $this->assertNotNull($dl);
        $this->assertTrue(strlen($dl) > 0);
        $dlCopy = $node->downloadAttachment("default");
        $this->assertNotNull($dlCopy);
        $this->assertTrue(strlen($dlCopy) > 0);
        $this->assertEquals($dl, $dlCopy);

        $headphonesImage = fopen("./res/headphones.png", "r");
        $node->uploadAttachment($headphonesImage, "image/png", "another");
        $attachments = $node->listAttachments();
        $this->assertEquals(2, sizeof($attachments));

        $node->deleteAttachment("default");
        $attachments = $node->listAttachments();
        $this->assertEquals(1, sizeof($attachments));

        $attachment = $attachments["another"];
        $this->assertEquals("another", $attachment->id);
        $this->assertEquals("another", $attachment->filename);
        $this->assertEquals("image/png", $attachment->contentType);
        $this->assertTrue($attachment->length > 0);
        $this->assertNotNull($attachment->objectId);
    }
}