<?php

namespace CloudCMS;

class Association extends BaseNode
{
    public function __construct($branch, $data)
    {
        parent::__construct($branch, $data);
    }

    public function getSourceTypeQName()
    {
        return $this->data["source_type"];
    }

    public function setSourceTypeQName($sourceTypeQName)
    {
        $this->data["source_type"] = $sourceTypeQName;
    }

    public function getSourceNodeId()
    {
        return $this->data["source"];
    }

    public function setSourceNodeId($sourceNodeId)
    {
        $this->data["source"] = $sourceNodeId;
    }

    public function getTargetTypeQName()
    {
        return $this->data["target_type"];
    }

    public function setTargetTypeQName($targetTypeQName)
    {
        $this->data["target_type"] = $targetTypeQName;
    }

    public function getTargetNodeId()
    {
        return $this->data["target"];
    }

    public function setTargetNodeId($targetNodeId)
    {
        $this->data["target"] = $targetNodeId;
    }

    public function getDirectionality()
    {
        return $this->data["directionality"];
    }

    public function setDirectionality($directionality)
    {
        $this->data["directionality"] = $directionality;
    }

    public function readSourceNode()
    {
        $nodeId = $this->getSourceNodeId();
        return $this->branch->readNode($nodeId);   
    }

    public function readTargetNode()
    {
        $nodeId = $this->getTargetNodeId();
        return $this->branch->readNode($nodeId);   
    }

    // Static

    public static function associationList($branch, $data)
    {
        $associations = array();
        foreach($data as $obj)
        {
            $association = new Association($branch, $obj);
            array_push($associations, $association);
        }

        return $associations;
    }
}