<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Table\ContentObject\Gallery\GalleryTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 * Renderer to display a sortable table with object publications.
 */
class GalleryTableContentObjectRenderer extends ContentObjectRenderer implements TableSupport
{

    /**
     * Returns the HTML output of this renderer.
     * 
     * @return string The HTML output
     */
    public function as_html()
    {
        $table = new GalleryTable($this);
        return $table->as_html();
    }
    
    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
