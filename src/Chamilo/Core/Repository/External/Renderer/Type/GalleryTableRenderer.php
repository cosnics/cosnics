<?php
namespace Chamilo\Core\Repository\External\Renderer\Type;

use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\External\Table\ExternalObjectGalleryTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 * Renderer to display a sortable table with learning object publications.
 */
class GalleryTableRenderer extends Renderer implements TableSupport
{

    /**
     *
     * @see common\libraries.NewGalleryObjectTableSupport::get_object_table_condition()
     */
    public function get_table_condition($object_table_class_name)
    {
        return $this->get_condition();
    }

    /**
     * Returns the HTML output of this renderer.
     * 
     * @return string The HTML output
     */
    public function as_html()
    {
        return ExternalObjectGalleryTable::factory($this)->as_html();
    }
}
