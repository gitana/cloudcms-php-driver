<?php

namespace CloudCMS;

class Node extends BaseNode
{
    public function __construct($branch, $data)
    {
        parent::__construct($branch, $data);
    }

    /**
     * Associates a target node to this node.
     * 
     * @param {String|Node} other_node - the id of the target node or the target node itself
     * @param {String} associationType - a string identifying the type QName of the association
     * @param {Object} association - a string identifying the type of association or a JSON object body for the new assocation node with the association type in _type
     * @param {Boolean} association_directionality - by default, will create a directed association. Can also set to to "UNDIRECTED" (aka mutual)
     */
    public function associate($other_node, $association_type, $association = array(), $association_directionality = Directionality::DIRECTED)
    {
        // $other_node could be a Node object or a sting. If a string then it should be a node id
        if ($other_node instanceof Node) {
            // pull the other node's id. that's all we need for the .../associate API call
            $other_node = $other_node->id;
        }

        $uri = $this->uri() . "/associate";
        $association_node = null;
        try
        {
            $params = array(
                "node" => $other_node,
                "type" => $association_type
            );

            if ($association_directionality != Directionality::DIRECTED)
            {
                $params["directionality"] = $association_directionality;
            }

            $res = $this->client->post($uri, $params, $association);

            $association_node = new Association($this->branch, $res);
        }
        catch (\GuzzleHttp\Exception\ClientException $ex)
        {
            // return null if not found
        }

        return $association_node;
    }
    
    public function unassociate($other_node, $association_type, $association_directionality = Directionality::DIRECTED)
    {
        $uri = $this->uri() . "/unassociate";
        $params = array();
        $params["node"] = $other_node->id;
        $params["type"] = $association_type;

        if ($association_directionality != Directionality::DIRECTED)
        {
            $params["directionality"] = $association_directionality;
        }

        $this->client->post($uri, $params);
    }

    public function associations($pagination = array(), $direction=Direction::ANY, $associationTypeQName = null)
    {
        $uri = $this->uri() . "/associations";
        $params = $pagination || array();
        $params["direction"] = $direction;

        if ($associationTypeQName != null)
        {
            $params["type"] = $associationTypeQName;
        }

        $res = $this->client->get($uri, $params);
        return Association::associationList($this->branch, $res["rows"]);
    }

    public function associateOf($sourceNode, $associationTypeQName, $data = array())
    {
        return $sourceNode->associate($this, $associationTypeQName, $data);
    }

    public function childOf($sourceNode)
    {
        return $this->associateOf($sourceNode, "a:child");
    }
    
    public function fileFolderTree($basePath = null, $leafPaths = array(), $depth = -1, $includeProperties = true, $containersOnly = false)
    {
        $uri = $this->uri() . "/tree";
        $params = array();

        if ($basePath != null)
        {
            $params["base"] = $basePath;
        }

        if ($leafPaths != null && count($leafPaths) > 0)
        {
            $leafsParam = implode(",", $leafPaths);
            $params["leaf"] = $leafsParam;
        }

        if ($depth > -1)
        {
            $params["depth"] = $depth;
        }

        if ($includeProperties)
        {
            $params["properties"] = $includeProperties;
        }

        if ($containersOnly)
        {
            $params["containers"] = $containersOnly;
        }

        return $this->client->post($uri, $params);
    }

    public function listChildren($pagination = array())
    {
        $uri = $this->uri() . "/children";
        $response = $this->client->get($uri, $pagination);
        return BaseNode::nodeList($this->branch, $response["rows"]);
    }

    public function listRelatives($typeQName, $direction, $pagination = array())
    {
        $uri = $this->uri() . "/relatives";
        $params = $pagination;
        $params["type"] = $typeQName;
        $params["direction"] = $direction;

        $res = $this->client->get($uri, $params);
        return BaseNode::nodeList($this->branch, $res["rows"]);
    }

    public function queryRelatives($typeQName, $direction, $query, $pagination = array())
    {
        $uri = $this->uri() . "/relatives/query";
        $params = $pagination;
        $params["type"] = $typeQName;
        $params["direction"] = $direction;
        
        $res = $this->client->post($uri, $params, $query);
        return BaseNode::nodeList($this->branch, $res["rows"]);
    }

    // def traverse(self, traverse_config):
    //     uri = self.uri() + '/traverse'

    //     body = { 'traverse': traverse_config }
    //     response = self.client.post(uri, data=body)
    //     results = TraversalResults.parse(response, self.branch)
    //     return results

    public function traverse($traverseConfig)
    {
        $uri = $this->uri() . "/traverse";
        $body = array("traverse" => $traverseConfig);
        $res = $this->client->post($uri, array(), $body);

        $results = TraversalResults::parse($res, $this->branch);
        return $results;
    }    

    public function resolvePath()
    {
        $uri = $this->uri() . "/path";
        $params = array("rootNodeId" => "821c40ab613d9b5bcbbc656b62229301"); // r:root or this?

        $response = $this->client->get($uri, $params);
        return $response["path"];
    }

    public function resolvePaths()
    {
        $uri = $this->uri() . "/paths";

        $response = $this->client->get($uri, array());
        return $response["paths"];
    }

    public function createTranslation($locale, $edition, $obj)
    {
        $uri = $this->uri() . "/i18n";

        $params = array();
        $params["locale"] = $locale;
        $params["edition"] = $edition;

        $res1 = $this->client->post($uri, $params, $obj);
        $nodeId = $res1["_doc"];

        // Read back node
        return $this->branch->readNode($nodeId);
    }

    public function getTranslationEditions()
    {
        $uri = $this->uri() . "/i18n/editions";
        $res = $this->client->get($uri);

        return $res["editions"];
    }

    public function getTranslationLocales($edition)
    {
        $uri = $this->uri() . "/i18n/locales";
        $params = array("edition" => $edition);

        $res = $this->client->get($uri, $params);
        return $res["locales"];
    }

    public function readTranslation($locale, $edition = null)
    {
        $uri = $this->uri() . "/i18n";
        $params = array();
        $params["locale"] = $locale;

        if ($edition != null)
        {
            $params["edition"] = $edition;
        }

        $res = $this->client->get($uri , $params);
        return new Node($this->branch, $res);
    }
}
