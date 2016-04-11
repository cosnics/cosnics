<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableCellRenderer;

class GalleryTableCellRenderer extends DataClassGalleryTableCellRenderer
{

    public function render_cell($content_object)
    {
        $html = array();

        $html[] = '<div class="panel panel-default panel-gallery">';

        $html[] = '<div class="panel-body panel-body-thumbnail">';
        $html[] = $this->get_cell_content($content_object);
        $html[] = '</div>';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $content_object->get_title() . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $this->get_modification_links($content_object);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_cell_content(ContentObject $content_object)
    {
        $display = ContentObjectRenditionImplementation :: factory(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_THUMBNAIL,
            $this->get_component());

        $html[] = '<a href="' . htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) .
             '">' . $display->render() . '</a>';

        return implode(PHP_EOL, $html);
    }

    private function get_modification_links($content_object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->get_component()->get_content_object_actions($content_object));
        return $toolbar->as_html();
    }
}
