<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

/**
 * This interface determines whether or not your table supports an action column (cell renderer usage)
 * 
 * @package \libraries;
 * @author Sven Vanpoucke
 */
interface TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     * 
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result);
}
