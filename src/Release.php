<?php

namespace CloudCMS;

class Release extends AbstractRepositoryDocument
{
    public function __construct($repository, $data)
    {
        parent::__construct($repository, $data);
    }

    public function uri()
    {
        return $this->repository->uri() . "/releases/" . $this->id;
    }

    
}