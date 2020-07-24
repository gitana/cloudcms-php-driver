<?php

namespace CloudCMS;

abstract class BaseNode extends AbstractRepositoryDocument
{
    protected $branch;
    public $branchId;

    public function __construct($branch, $data)
    {
        parent::__construct($branch->repository, $data);

        $this->branch = $branch;
        $this->branchId = $branch->id;
    }

    public function uri()
    {
        return $this->branch->uri() . "/nodes/" . $this->id;
    }

    public function downloadAttachment($attachmentId = "default")
    {
        $uri = $this->uri() . "/attachments/" . $attachmentId;
        return $this->client->download($uri);
    }

    public function uploadAttachment($file, $contentType, $attachmentId = "default", $filename = null)
    {
        $uri = $this->uri() . "/attachments/" . $attachmentId;
        $name = $attachmentId;
        if ($filename != null)
        {
            $name = $filename;
        }

        return $this->client->upload($uri, $name, $file, $contentType);
    }

    public function deleteAttachment($attachmentId = "default")
    {
        $uri = $this->uri() . "/attachments/" . $attachmentId;
        return $this->client->delete($uri);
    }

    public function listAttachments()
    {
        $uri = $this->uri() . "/attachments";
        $response = $this->client->get($uri);
        
        return Attachment::attachmentMap($this, $response["rows"]);
    }

    public function getFeatureIds()
    {
        $featuresObj = $this->data["_features"] ?? array();
        return array_keys($featuresObj);
    }

    public function getFeature($featureId)
    {
        $featuresObj = $this->data["_features"] ?? array();
        return $featuresObj[$featureId] ?? null;
    }

    public function hasFeature($featureId)
    {
        $features_obj = $this->data["_features"] ?? array();
        return array_key_exists($featureId, $features_obj);
    }

    public function addFeature($featureId, $featureConfig)
    {
        $uri = $this->uri() . "/features/" . $featureId;
        $this->client->post($uri, array(), $featureConfig);
        $this->reload();
    }
    
    public function removeFeature($featureId)
    {
        $uri = $this->uri() . "/features/" . $featureId;
        $this->client->delete($uri);
        $this->reload();
    }

    // Static

    public static function buildNode($branch, $data, $forceAssociation = false)
    {
        if ($forceAssociation || (array_key_exists("is_association", $data) && $data["is_association"] == true))
        {
            return new Association($branch, $data);
        }
        else
        {
            return new Node($branch, $data);
        }
    }

    public static function nodeList($branch, $data)
    {
        $nodes = array();
        foreach($data as $obj)
        {
            $node = BaseNode::buildNode($branch, $obj);
            array_push($nodes, $node);
        }

        return $nodes;
    }
}