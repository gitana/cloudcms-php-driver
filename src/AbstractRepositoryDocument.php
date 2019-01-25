<?php

namespace CloudCMS;

abstract class AbstractRepositoryDocument extends AbstractDocument
{
    protected $repository;
    public $repositoryId;
    public $platformId;

    public function __construct($repository, $data)
    {
        parent::__construct($repository->client, $data);

        $this->repository = $repository;
        $this->repositoryId = $repository->id;
        $this->platformId = $repository->platformId;
    }
}