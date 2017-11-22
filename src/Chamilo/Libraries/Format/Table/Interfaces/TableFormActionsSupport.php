<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

/**
 * This interface determines whether or not your table supports form actions
 * If this is the case then the table needs to implement the function get_implemented_form_actions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface TableFormActionsSupport
{

    /**
     * Returns the implemented form actions
     *
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function get_implemented_form_actions();
}
