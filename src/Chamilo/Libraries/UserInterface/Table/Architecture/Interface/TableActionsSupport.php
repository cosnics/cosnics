<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\FormAction\TableActions;

/**
 * @package Chamilo\Libraries\Format\Table\Interface
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableActionsSupport
{
    public function getTableActions(): TableActions;
}
