<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\GlossaryRenderer;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type\Table\GlossaryViewerTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 * Class to render the glossary as a table
 *
 * @package repository\content_object\glossary
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TableGlossaryRenderer extends GlossaryRenderer implements TableSupport
{

    /**
     * Renders the glossary
     *
     * @return string
     */
    public function render()
    {
        $table = new GlossaryViewerTable($this);
        return $table->as_html();
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_parameters()
     */
    public function get_parameters()
    {
        return $this->get_component()->get_parameters();
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return null;
    }
}