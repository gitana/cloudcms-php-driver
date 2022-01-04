<?php

namespace CloudCMS;

class Project extends AbstractPlatformDocument
{
    public function __construct($platform, $data)
    {
        parent::__construct($platform, $data);
    }

    public function uri()
    {
        return $this->platform->uri() . "/projects/" . $this->id;
    }

    
}
