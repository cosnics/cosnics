<?php
namespace Chamilo\Core\Repository\Implementation\Slideshare\Table\ExternalObjectGallery;

use Chamilo\Core\Repository\External\ExternalObjectDisplay;
use Chamilo\Core\Repository\External\Table\ExternalObjectGallery\DefaultExternalObjectGalleryTableCellRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExternalObjectGalleryTableCellRenderer extends DefaultExternalObjectGalleryTableCellRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer::renderContent()
     */
    public function renderContent($object)
    {
        $html = array();
        $display = ExternalObjectDisplay::factory($object);
        $html[] = '<a href="' . $this->get_component()->get_external_repository_object_viewing_url($object) . '">' . $display->get_preview(
            true) . '</a>';
        
        if ($object->get_description())
        {
            $html[] = '<br/>';
            $html[] = '<i>' . StringUtilities::getInstance()->truncate($object->get_description(), 100) . '</i>';
            $html[] = '<br/>';
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer::renderTitle()
     */
    public function renderTitle($object)
    {
        return StringUtilities::getInstance()->truncate($object->get_title(), 25);
    }
}
