<?php
namespace Chamilo\Core\Repository\External\Renderer\Type;

use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\External\Table\ExternalObjectTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

class TableRenderer extends Renderer implements TableSupport
{

    /**
     *
     * @see common\libraries.NewObjectTableSupport::get_object_table_condition()
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
        return ExternalObjectTable :: factory($this)->as_html();
    }
}
