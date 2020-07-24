<?php

namespace CloudCMS;

class Attachment
{
    public $attachable;
    public $id;
    public $objectId;
    public $length;
    public $filename;
    public $contentType;

    public function __construct($attachable, $obj)
    {
        $this->attachable = $attachable;
        $this->id = $obj["attachmentId"];
        $this->objectId = $obj["objectId"];
        $this->length = $obj["length"];
        $this->filename = $obj["filename"];
        $this->contentType = $obj["contentType"];
    }
    
    public function downloadAttachment()
    {
        return $this->attachable->downloadAttachment($this->id);
    }

    // Static
    public static function attachmentMap($attachable, $data)
    {
        $attachments = array();
        foreach($data as $obj)
        {
            $attachment = new Attachment($attachable, $obj);
            $attachments[$attachment->id] = $attachment;
        }

        return $attachments;
    }
    
}