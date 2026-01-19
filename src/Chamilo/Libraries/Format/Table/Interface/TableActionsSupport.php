<?php
namespace Chamilo\Libraries\Format\Table\Interface;

use Chamilo\Libraries\Format\Table\FormAction\TableActions;

/**
 * @package Chamilo\Libraries\Format\Table\Interface
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableActionsSupport
{
    public function getTableActions(): TableActions;
}
