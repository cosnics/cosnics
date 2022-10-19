<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

/**
 * This interface determines whether or not your table supports an action column (cell renderer usage)
 *
 * @package Chamilo\Libraries\Format\Table\Interfaces
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableRowActionsColumnSupport
{

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     */
    public function getActions($result): string;
}
