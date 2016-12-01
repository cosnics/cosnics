<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video\Table\ExternalObjectGallery;

use Chamilo\Core\Repository\External\Table\ExternalObjectGallery\DefaultExternalObjectGalleryTableCellRenderer;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExternalObjectGalleryTableCellRenderer extends DefaultExternalObjectGalleryTableCellRenderer
{

    public function renderContent($object)
    {
        $html = array();
        
        $html[] = '<a href="' . $this->get_component()->get_external_repository_object_viewing_url($object) .
             '"><img width="100%" class="thumbnail" src="' . $object->get_thumbnail() . '"/></a> <br/>';
        $html[] = '<i>' . StringUtilities::getInstance()->truncate($object->get_description(), 100) . '</i><br/>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer::renderTitle()
     */
    public function renderTitle($content_object)
    {
        return StringUtilities::getInstance()->truncate($content_object->get_title(), 25) . ' (' . DatetimeUtilities::format_seconds_to_minutes(
            $content_object->get_duration()) . ')';
    }
}
