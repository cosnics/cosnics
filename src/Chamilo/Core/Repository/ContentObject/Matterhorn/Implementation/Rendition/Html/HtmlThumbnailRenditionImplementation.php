<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Matterhorn\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

class HtmlThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        $object = $this->get_object();
        $media_package = $object->get_matterhorn_media_package();
        $html = array();
        $html[] = Translation :: get('MediaFinished') . '<br/>';
        $html[] = '<h3>' . $object->get_title() . '</h3>';
        $html[] = '<div>' . $object->get_description() . '</div>';
        $html[] = $this->get_properties_table();
        
        return implode("\n", $html);
    }
}
