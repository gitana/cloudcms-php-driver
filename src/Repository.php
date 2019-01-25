<?php

namespace CloudCMS;

class Repository extends AbstractDocument
{
    public $platformId;

    public function __construct($client, $data)
    {
        parent::__construct($client, $data);

        $this->platformId = $data["platformId"];
    }

    public function uri()
    {
        return "/repositories/" . $this->id;
    }

    public function listBranches()
    {
        $uri = $this->uri() . "/branches";
        $res = $this->client->get($uri);

        $branches = array();
        foreach($res["rows"] as $row)
        {
            $branch = new Branch($this, $row);
            array_push($branches, $branch);
        }

        return $branches;
    }

    public function readBranch($branchId)
    {
        $uri = $this->uri() . "/branches/" . $branchId;
        $branch = null;
        try
        {
            $res = $this->client->get($uri);
            $branch = new Branch($this, $res);
        }
        catch (\GuzzleHttp\Exception\ClientException $ex)
        {
            // return null if not found
        }

        return $branch;
    }
    
}