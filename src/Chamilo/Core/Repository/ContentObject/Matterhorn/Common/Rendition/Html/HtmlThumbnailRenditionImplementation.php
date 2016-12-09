<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Matterhorn\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\Implementation\Matterhorn\Attachment;

class HtmlThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object()->get_synchronization_data()->get_external_object();
        $settings = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'url',
            $object->get_external_repository_id());

        $search_preview = $object->get_search_preview();

        if ($search_preview instanceof Attachment)
        {
            $width = 320;
            $height = 356;
            return '<img class="thumbnail" src="' . $object->get_search_preview()->get_url() . '"/>';
        }
        else
        {
            return ContentObjectRendition :: factory($this)->render();
        }
    }
}
