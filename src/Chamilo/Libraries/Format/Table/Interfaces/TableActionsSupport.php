<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

use Chamilo\Libraries\Format\Table\FormAction\TableActions;

/**
 * This interface determines whether or not your table supports an action column (cell renderer usage)
 *
 * @package Chamilo\Libraries\Format\Table\Interfaces
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableActionsSupport
{
    public function getTableActions(): TableActions;
}
