<?php

namespace CloudCMS;

class Branch extends AbstractRepositoryDocument
{
    public $platformId;

    public function __construct($repository, $data)
    {
        parent::__construct($repository, $data);
    }

    public function uri()
    {
        return $this->repository->uri() . "/branches/" . $this->id;
    }

    public function isMaster()
    {
        return $this->data["type"] === "MASTER";
    }

    public function readNode($nodeId)
    {
        $uri = $this->uri() . "/nodes/" . $nodeId;
        $node = null;
        try
        {
            $res = $this->client->get($uri);
            $node = new Node($this, $res);
        }
        catch (\GuzzleHttp\Exception\ClientException $ex)
        {
            // return null if not found
        }

        return $node;
    }

    public function queryNodes($query, $pagination = array())
    {
        $uri = $this->uri() . "/nodes/query";
        $res = $this->client->post($uri, $pagination, $query);

        $nodeList = Node::nodeList($this, $res["rows"]);
        return $nodeList;
    }

    public function findNodes($config, $pagination = array())
    {
        $uri = $this->uri() . "/nodes/find";
        $res = $this->client->post($uri, $pagination, $config);

        $nodeList = Node::nodeList($this, $res["rows"]);
        return $nodeList;
    }

    public function createNode($obj, $options = array())
    {
        $uri = $this->uri() . "/nodes";

        $params = array();
        $params["rootNodeId"] = isset($options["rootNodeId"]) ? $options["rootNodeId"] : "root";
        $params["associationType"] = isset($options["associationType"]) ? $options["associationType"] : "a:child";

        if (isset($options["parentFolderPath"]))
        {
            $params["parentFolderPath"] = $options["parentFolderPath"];
        }
        else if (isset($options["folderPath"]))
        {
            $params["parentFolderPath"] = $options["folderPath"];
        }
        else if (isset($options["folderpath"]))
        {
            $params["parentFolderPath"] = $options["folderpath"];
        }

        if (isset($options["filePath"]))
        {
            $params["filePath"] = $options["filePath"];
        }
        else if (isset($options["filepath"]))
        {
            $params["filePath"] = $options["filepath"];
        }

        $res = $this->client->post($uri, $params, $obj);
        $nodeId = $res["_doc"];

        return $this->readNode($nodeId);
    }

    public function deleteNodes($nodeIds)
    {
        $uri = $this->uri() . "/nodes/delete";
        $this->client->post(uri, $nodeIds);
    }
}