<?php

namespace CloudCMS;

abstract class AbstractRepositoryDocument extends AbstractPlatformDocument
{
    protected $repository;
    public $repositoryId;

    public function __construct($repository, $data)
    {
        parent::__construct($repository->platform, $data);

        $this->repository = $repository;
        $this->repositoryId = $repository->id;
    }
}