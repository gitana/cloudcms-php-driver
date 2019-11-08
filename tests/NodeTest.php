<?php

namespace CloudCMS\Test;

use CloudCMS\Node;

final class NodeTest extends AbstractWithRepositoryTest
{
    public $branch;

    /**
     * @before
     */
    public function setupBranch()
    {
        $this->branch = self::$repository->readBranch("master");
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

    public function testNodeQueryAndFind()
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
        sleep(5);

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
        
        $node1->delete();
        $node2->delete();
        $node3->delete();
        $node4->delete();
    }

    public function testNodeAssociation()
    {
        $nodeObj1 = array(
            "title" => "Cheese burger",
            "meal" => "lunch"
        );
        $nodeObj2 = array(
            "title" => "Ham burger",
            "meal" => "lunch"
        );

        $node1 = $this->branch->createNode($nodeObj1);
        $node2 = $this->branch->createNode($nodeObj2);

        $associationNode = $node1->associate($node2, "a:linked", true);

        $this->assertNotNull($associationNode->id);
        $this->assertTrue($associationNode instanceof Node);

        $node1->delete();
        $node2->delete();
    }
}