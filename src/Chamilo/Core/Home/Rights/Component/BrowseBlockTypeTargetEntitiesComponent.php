<?php

namespace Chamilo\Core\Home\Rights\Component;

use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Home\Rights\Table\BlockTypeTargetEntity\BlockTypeTargetEntityTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Browses the target entities for the block types
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowseBlockTypeTargetEntitiesComponent extends Manager implements TableSupport
{
    /**
     * Executes this component and renders it's output
     */
    function run()
    {
        if (! $this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = array();
        
        $tableContent = $this->renderTable();
        
        $html[] = $this->render_header();
        $html[] = $tableContent;
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the table
     *
     * @return string
     */
    protected function renderTable()
    {
        $table = new BlockTypeTargetEntityTable($this);
        return $table->as_html();
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {

    }
}