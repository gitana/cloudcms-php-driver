<?php

namespace CloudCMS;

class TraversalResults
{
    public $nodes;
    public $associations;

    public function __construct()
    {
        $this->nodes = array();
        $this->associations = array();
    }

    // Static

    public static function parse($response, $branch)
    {
        $result = new TraversalResults();

        $nodesObj = $response["nodes"];

        foreach ($nodesObj as $id => $nodeObj)
        {
            $node = BaseNode::buildNode($branch, $nodeObj);
            array_push($result->nodes, $node);
        }

        $associationsObj = $response["associations"];
        foreach ($associationsObj as $id => $associationObj)
        {
            $association = BaseNode::buildNode($branch, $nodeObj, true);
            array_push($result->associations, $association);
        }

        return $result;
    }
}