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
            $repository = new Repository($this, $row);
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
            $repository = new Repository($this, $res);
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

    public function readJob($jobId)
    {
        $uri = $this->uri() . "/jobs/" . $jobId;
        $res = $this->client->get($uri);

        return new Job($this->client, $res);
    }

    public function queryJobs($query, $pagination = array())
    {
        $uri = $this->uri() . "/jobs/query";
        $res = $this->client->post($uri, $pagination, $query);

        $jobs = array();
        foreach($res["rows"] as &$row)
        {
            array_push($jobs, new Job($this->client, $row));
        }

        return $jobs;
    }

    // Projects

    public function readProject($projectId)
    {
        $uri = $this->uri() . "/projects/" . $projectId;
        $res = $this->client->get($uri);

        return new Project($this, $res);
    }

    public function listProjects($pagination = array())
    {
        $uri = $this->uri() . "/projects";
        $res = $this->client->get($uri, $pagination);

        $projects = array();
        foreach ($res["rows"] as &$project)
        {
            array_push($projects, new Project($this, $project));
        }

        return $projects;
    }

    public function queryProjects($query, $pagination = array())
    {
        $uri = $this->uri() . "/projects/query";
        $res = $this->client->post($uri, $pagination, $query);

        $projects = array();
        foreach ($res["rows"] as &$project)
        {
            array_push($projects, new Project($this, $project));
        }

        return $projects;
    }

    public function startCreateProject($obj = array())
    {
        $uri = $this->uri() . "/projects/start";
        $res = $this->client->post($uri, array(), $obj);

        $jobId = $res["_doc"];
        return $this->readJob($jobId);
    }
}