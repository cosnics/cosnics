<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;

class GalleryTableCellRenderer extends DataClassGalleryTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    public function renderContent($content_object)
    {
        $display = ContentObjectRenditionImplementation::factory(
            $content_object, 
            ContentObjectRendition::FORMAT_HTML, 
            ContentObjectRendition::VIEW_THUMBNAIL, 
            $this->get_component());
        
        $html[] = '<a href="' . htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) .
             '">' . $display->render() . '</a>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTableCellRenderer::renderTitle()
     */
    public function renderTitle($content_object)
    {
        return $content_object->get_title();
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($content_object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->get_component()->get_content_object_actions($content_object));
        return $toolbar->as_html();
    }
}
