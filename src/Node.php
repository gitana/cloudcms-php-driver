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

    /**
     * Associates a target node to this node.
     * 
     * @param {String|Node} other_node - the id of the target node or the target node itself
     * @param {Object|String} association - a string identifying the type of association or a JSON object body for the new assocation node with the association type in _type
     * @param {Boolean} association_directionality - if true, a directed association is created. Otherwise the association will be undirected (i.e. mutual)
     */
    public function associate($other_node, $association, $association_directionality = false)
    {
        // $other_node could be a Node object or a sting. If a string then it should be a node id
        if ($other_node instanceof Node) {
            // pull the other node's id. that's all we need for the .../associate API call
            $other_node = $other_node->id;
        }

        // if $association is a string assume it is the type of the association to create
        // if an object assume it is the complete set of properties for the assocation node to create
        if (is_a($association, 'String')) {
            $association = array("_type" => $association);
        }

        $uri = $this->uri() . "/associate";
        $association_node = null;
        try
        {
            $params = array("node" => $other_node);
            $res = $this->client->post($uri, $params, $association);
            $association_node = new Node($this, $res);
        }
        catch (\GuzzleHttp\Exception\ClientException $ex)
        {
            // return null if not found
        }

        return $association_node;
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
