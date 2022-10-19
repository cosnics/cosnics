<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;

/**
 * This interface determines whether or not your table supports form actions
 * If this is the case then the table needs to implement the function get_implemented_form_actions
 *
 * @package Chamilo\Libraries\Format\Table\Interfaces
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableFormActionsSupport
{

    public function get_implemented_form_actions(): TableFormActions;
}
