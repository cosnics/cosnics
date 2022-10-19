<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

/**
 * This interface forces components that uses a table to implement the necessary functions.
 * Without implementing
 * this interface you are not able to use a table
 *
 * @package Chamilo\Libraries\Format\Table\Interfaces
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableSupport
{

    /**
     * Returns the parameters that the table needs for the url building
     *
     * @return string[]
     */
    public function get_parameters();

    /**
     * Returns the condition
     *
     * @param string $tableClassname
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($tableClassname);
}
