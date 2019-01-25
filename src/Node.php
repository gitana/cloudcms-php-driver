<?php

namespace CloudCMS;

class Node extends AbstractRepositoryDocument
{
    protected $branch;
    public $branchId;

    public function __construct($branch, $data)
    {
        parent::__construct($branch->repository, $data);

        $this->branch = $branch;
        $this->branchId = $branch->id;
    }

    public function uri()
    {
        return $this->branch->uri() . "/nodes/" . $this->id;
    }

    // Static

    public static function nodeList($branch, $data)
    {
        $nodes = array();
        foreach($data as $obj)
        {
            $node = new Node($branch, $obj);
            array_push($nodes, $node);
        }

        return $nodes;
    }
}
