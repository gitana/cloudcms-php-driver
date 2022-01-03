<?php

namespace CloudCMS;

use \ArrayObject;

class Platform extends AbstractDocument
{
    public function __construct($client, $data)
    {
        parent::__construct($client, $data);
    }

    public function uri()
    {
        return "";
    }

    public function listRepositories()
    {
        $uri = $this->uri() . "/repositories";
        $res = $this->client->get($uri);
        
        $repositories = array();
        foreach($res["rows"] as $row)
        {
            $repository = new Repository($this->client, $row);
            array_push($repositories, $repository);
        }

        return $repositories;
    }

    public function readRepository($repositoryId)
    {
        $uri = $this->uri() . "/repositories/" . $repositoryId;
        $repository = null;
        try
        {
            $res = $this->client->get($uri);
            $repository = new Repository($this->client, $res);
        }
        catch (\GuzzleHttp\Exception\ClientException $ex)
        {
            // return null if not found
        }

        return $repository;
    }

    public function createRepository($obj = array())
    {
        $uri = $this->uri() . "/repositories";
        $res = $this->client->post($uri, $obj);

        $repositoryId = $res["_doc"];
        return $this->readRepository($repositoryId);
    }
}