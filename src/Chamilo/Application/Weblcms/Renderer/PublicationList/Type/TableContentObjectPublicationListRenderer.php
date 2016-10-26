<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

/**
 * Renderer to display a sortable table with learning object publications.
 *
 * @package application.weblcms
 * @author Hans De Bisschop - EHB
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class TableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer implements TableSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the HTML output of this renderer.
     *
     * @return string The HTML output
     */
    public function as_html()
    {
        $context = $this->get_tool_browser()->get_parent()->package();
        $publication_table_class = $context . '\Table\Publication\PublicationTable';

        if (class_exists($publication_table_class))
        {
            $table = new $publication_table_class($this);
        }
        else
        {
            if (method_exists($this->get_tool_browser()->get_parent(), 'get_publication_table'))
            {
                $table = $this->get_tool_browser()->get_parent()->get_publication_table($this);
            }
            else
            {
                $table = new ObjectPublicationTable($this);
            }
        }

        return $table->as_html();
    }

    /**
     * Returns the parameters that the table needs for the url building
     *
     * @return string[]
     */
    public function get_parameters()
    {
        return $this->get_tool_browser()->get_parameters();
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        return $this->get_publication_conditions();
    }
}
