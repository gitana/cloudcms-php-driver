<?php

namespace CloudCMS;

abstract class AbstractDocument
{
    protected $client;
    public $id;
    public $data;

    public function __construct($client, $data)
    {
        $this->client = $client;
        $this->id = $data["_doc"];

        $this->data = $data;
    }

    public abstract function uri();

    public function reload()
    {
        try
        {
            $newData = $this->client->get($this->uri());
            $this->data = $newData;
        }
        catch (\GuzzleHttp\Exception\ClientException $ex)
        {
            $newData = null;
        }        
    }

    public function delete()
    {
        $this->client->delete($this->uri());
    }

    public function update()
    {
        $this->client->put($this->uri(), array(), $this->data);
    }
}