<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\File\Path;

class HtmlThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();
        if ($object->is_image())
        {
            $width = 100;
            $height = 100;
            
            $thumbnail_path = Path :: getInstance()->getTemporaryPath() . md5($object->get_full_path()) .
                 basename($object->get_full_path());
            $thumbnal_web_path = Path :: getInstance()->getTemporaryPath(null, true) . md5($object->get_full_path()) .
                 basename($object->get_full_path());
            if (! is_file($thumbnail_path))
            {
                $thumbnail_creator = ImageManipulation :: factory($object->get_full_path());
                $thumbnail_creator->scale($width, $height);
                $thumbnail_creator->write_to_file($thumbnail_path);
            }
            return '<img src="' . $thumbnal_web_path . '" title="' . $object->get_title() . '" class="thumbnail" />';
        }
        else
        {
            return ContentObjectRendition :: launch($this);
        }
    }
}
