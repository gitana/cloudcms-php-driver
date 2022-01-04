<?php

namespace CloudCMS;

class Job extends AbstractDocument
{
    public function __construct($client, $data)
    {
        parent::__construct($client, $data);
    }

    public function uri()
    {
        return "/jobs/" . $this->id;
    }

    public function kill()
    {
        $uri = $this->uri() . "/kill";
        $this->client->post($uri);
    }

    public function waitForCompletion()
    {
        while (true)
        {
            $this->reload();
            if (strcmp($this->data["state"], "FINISHED") == 0)
            {
                return;
            }
            else if (strcmp($this->data["state"], "ERROR") == 0)
            {
                throw new CloudCMSException("Job Failed: " . $this->id);
            }
            else
            {
                sleep(1);
            }
        }
    }

}