<?php

namespace CloudCMS\Test;

use CloudCMS\Node;
use CloudCMS\Directionality;
use CloudCMS\Direction;

final class AssociationTest extends AbstractWithRepositoryTest
{
    public function testAssociateUnassociate()
    {
        $node1 = $this->branch->createNode(array("title" => "node1"));
        $node2 = $this->branch->createNode(array("title" => "node2"));
        $node3 = $this->branch->createNode(array("title" => "node3"));

        // Associate node 1 directed to node 2 with a:child
        $association1 = $node1->associate($node2, "a:child");
        $this->assertEquals(Directionality::DIRECTED, $association1->getDirectionality());
        $this->assertEquals($node1->id, $association1->getSourceNodeId());
        $this->assertEquals($node2->id, $association1->getTargetNodeId());

        $source = $association1->readSourceNode();
        $this->assertEquals($node1->id, $source->id);
        $target = $association1->readTargetNode();
        $this->assertEquals($node2->id, $target->id);

        // Associate node 1 undirected to node 3 with a:linked
        $association2 = $node1->associate($node3, "a:linked", array("test" => "field"),  Directionality::UNDIRECTED);
        $this->assertEquals(Directionality::UNDIRECTED, $association2->getDirectionality());
        $this->assertEquals($node1->id, $association2->getSourceNodeId());
        $this->assertEquals($node3->id, $association2->getTargetNodeId());
        $this->assertEquals("field", $association2->data["test"]);

        $allAssociations = $node1->associations();
        $this->assertEquals(3, sizeof($allAssociations));

        $outgoingAssociations = $node1->associations(array(), Direction::OUTGOING);
        $this->assertEquals(2, sizeof($outgoingAssociations));

        $incomingAssociations = $node1->associations(array(), Direction::INCOMING);
        $this->assertEquals(2, sizeof($incomingAssociations));
        
        $childAssociations = $node1->associations(array(), Direction::ANY, "a:child");
        $this->assertEquals(1, sizeof($childAssociations));

        $node1->unassociate($node2, "a:child");
        $node1->unassociate($node3, "a:linked", Directionality::UNDIRECTED);

        $allAssociations = $node1->associations();
        $this->assertEquals(1, sizeof($allAssociations));
    }

    public function testChildOf()
    {
        $node1 = $this->branch->createNode(array("title" => "node1"));
        $node2 = $this->branch->createNode(array("title" => "node2"));

        $association = $node1->childOf($node2);
        $this->assertNotNull($association);
        $this->assertEquals(Directionality::DIRECTED, $association->getDirectionality());

        $source = $association->readSourceNode();
        $this->assertEquals($node2->id, $source->id);
        $target = $association->readTargetNode();
        $this->assertEquals($node1->id, $target->id);
    }
}