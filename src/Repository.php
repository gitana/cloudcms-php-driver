<?php

namespace CloudCMS;

class Repository extends AbstractPlatformDocument
{
    public function __construct($platform, $data)
    {
        parent::__construct($platform, $data);

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

    public function queryBranches($query, $pagination = array())
    {
        $uri = $this->uri() . "/branches/query";
        $res = $this->client->post($uri, $pagination, $query);

        $branches = array();
        foreach($res["rows"] as $row)
        {
            $branch = new Branch($this, $row);
            array_push($branches, $branch);
        }

        return $branches;
    }

    public function startCreateBranch($obj, $sourceBranchId, $sourceChangesetId = null)
    {
        $uri = $this->uri() . "/branches/create/start";
        $params = array("branch" => $sourceBranchId);

        if ($sourceChangesetId != null)
        {
            $params["changeset"] = $sourceChangesetId;
        }

        $res = $this->client->post($uri, $params, $obj);
        $jobId = $res["_doc"];

        return $this->platform->readJob($jobId);
    }

    // Releases
    public function listReleases($pagination = array())
    {
        $uri = $this->uri() . "/releases";
        $res = $this->client->get($uri, $pagination);

        $releases = array();
        foreach($res["rows"] as &$release)
        {
            array_push($releases, new Release($this, $release));
        }

        return $releases;
    }

    public function queryReleases($query, $pagination = array())
    {
        $uri = $this->uri() . "/releases/query";
        $res = $this->client->post($uri, $pagination, $query);

        $releases = array();
        foreach($res["rows"] as &$release)
        {
            array_push($releases, new Release($this, $release));
        }

        return $releases;
    }

    public function readRelease($releaseId)
    {
        $uri = $this->uri() . "/releases/" . $releaseId;
        $res = $this->client->get($uri);

        return new Release($this, $res);
    }

    public function startCreateRelease($obj, $sourceReleaseId = null)
    {
        $uri = $this->uri() . "/releases/create/start";
        $params = array();

        if ($sourceReleaseId != null)
        {
            $params["sourceId"] = $sourceReleaseId;
        }

        $res = $this->client->post($uri, $params, $obj);
        $jobId = $res["_doc"];

        return $this->platform->readJob($jobId);
    }

    
}