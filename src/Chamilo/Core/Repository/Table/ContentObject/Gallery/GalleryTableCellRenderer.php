<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableCellRenderer;
use Chamilo\Libraries\Utilities\Utilities;

class GalleryTableCellRenderer extends DataClassGalleryTableCellRenderer
{

    public function render_cell($content_object)
    {
        $html = array();
        
        $html[] = $this->get_cell_content($content_object);
        $html[] = '<div class="thumbnail_action">';
        $html[] = $this->get_modification_links($content_object);
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    public function get_cell_content(ContentObject $content_object)
    {
        $display = ContentObjectRenditionImplementation :: factory(
            $content_object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_THUMBNAIL, 
            $this->get_component());
        
        $html[] = '<h4>' . Utilities :: truncate_string($content_object->get_title(), 25, false) . '</h4>';
        $html[] = '<a href="' . htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) .
             '">' . $display->render() . '</a>';
        
        return implode("\n", $html);
    }

    private function get_modification_links($content_object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->get_component()->get_content_object_actions($content_object));
        return $toolbar->as_html();
    }
}
