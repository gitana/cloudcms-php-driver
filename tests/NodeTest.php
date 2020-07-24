<?php

namespace CloudCMS\Test;

use CloudCMS\Node;
use CloudCMS\Directionality;

final class NodeTest extends AbstractWithRepositoryTest
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
    
    public function testNodeCrud()
    {
        $nodeObj = array(
            "title" => "MyNode"
        );
        $node = $this->branch->createNode($nodeObj);

        $nodeRead = $this->branch->readNode($node->id);
        $this->assertEquals($node, $nodeRead);

        $node->data["title"] = "NewTitle";
        $node->update();

        $nodeRead = $this->branch->readNode($node->id);
        $this->assertEquals($node->data["title"], $nodeRead->data["title"]);
        
        $node->delete();

        $nodeRead = $this->branch->readNode($node->id);
        $this->assertNull($nodeRead);        
    }

    public function testNodeNestedArray()
    {
        $nodeObj = array(
            "title" => "MyNode",
            "nested" => array('1',' 2', '3')
        );
        $node = $this->branch->createNode($nodeObj);

        $this->assertEquals(3, count($node->data["nested"]));  
    }

    public function testNodeQuerySearchFind()
    {
        $nodeObj1 = array(
            "title" => "Cheese burger",
            "meal" => "lunch"
        );
        $nodeObj2 = array(
            "title" => "Ham burger",
            "meal" => "lunch"
        );
        $nodeObj3 = array(
            "title" => "Turkey sandwich",
            "meal" => "lunch"
        );
        $nodeObj4 = array(
            "title" => "Oatmeal",
            "meal" => "breakfast"
        );

        $node1 = $this->branch->createNode($nodeObj1);
        $node2 = $this->branch->createNode($nodeObj2);
        $node3 = $this->branch->createNode($nodeObj3);
        $node4 = $this->branch->createNode($nodeObj4);

        // Wait for nodes to index
        sleep(20);

        $query = array(
            "meal" => "lunch"
        );
        $queryNodes = $this->branch->queryNodes($query);
        $this->assertContainsOnlyInstancesOf(Node::class, $queryNodes);
        $this->assertEquals(3, sizeof($queryNodes));
        $queryNodesIds = array_column($queryNodes, "id");
        $this->assertContains($node1->id, $queryNodesIds);
        $this->assertContains($node2->id, $queryNodesIds);
        $this->assertContains($node3->id, $queryNodesIds);

        $find = array(
            "search" => "burger"
        );
        $findNodes = $this->branch->findNodes($find);
        $this->assertContainsOnlyInstancesOf(Node::class, $findNodes);
        $this->assertEquals(2, sizeof($findNodes));
        $findNodesIds = array_column($findNodes, "id");
        $this->assertContains($node1->id, $findNodesIds);
        $this->assertContains($node2->id, $findNodesIds);

        $searchNodes = $this->branch->searchNodes("burger");
        $this->assertContainsOnlyInstancesOf(Node::class, $searchNodes);
        $this->assertEquals(2, sizeof($searchNodes));
        $searchNodeIds = array_column($searchNodes, "id");
        $this->assertContains($node1->id, $searchNodeIds);
        $this->assertContains($node2->id, $searchNodeIds);        
        
        $node1->delete();
        $node2->delete();
        $node3->delete();
        $node4->delete();
    }

    public function testFeatures()
    {
        $node = $this->branch->createNode(array());
        $featureIds = $node->getFeatureIds();
        $this->assertTrue(sizeof($featureIds) > 0);

        $node->addFeature("f:filename", array("filename" => "file1"));
        $featureIds = $node->getFeatureIds();
        $this->assertContains("f:filename", $featureIds);
        $this->assertTrue($node->hasFeature("f:filename"));
        $featureObj = $node->getFeature("f:filename");
        $this->assertEquals("file1", $featureObj["filename"]);

        $node->removeFeature("f:filename");
        $featureIds = $node->getFeatureIds();
        $this->assertNotContains("f:filename", $featureIds);
        $this->assertFalse($node->hasFeature("f:filename"));
        $this->assertNull($node->getFeature("f:filename"));

        $node->delete();
    }

    public function testTranslations()
    {
        $rootNode = $this->branch->rootNode();

        $node = $this->createFile($this->branch, $rootNode, "theNode", false);
        $german = $node->createTranslation("de_DE", "1.0", array("title" => "german node"));
        $this->assertNotNull($german);

        $spanish1 = $node->createTranslation("es_ES", "1.0", array("title" => "spanish node"));
        $spanish2 = $node->createTranslation("es_ES", "2.0", array("title" => "spanish node 2"));

        $editions = $node->getTranslationEditions();
        $this->assertEquals(2, sizeof($editions));

        $locales = $node->getTranslationLocales("1.0");
        $this->assertEquals(2, sizeof($locales));

        $translation = $node->readTranslation("es_MX", "2.0");
        $this->assertEquals("spanish node 2", $translation->data["title"]);
    }
}