<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;

/**
 * $Id: admin_request_browser_table.class.php 218 2009-11-13 14:21:26Z Yannick $
 *
 * @package application.lib.weblcms.weblcms_manager.component.admin_request_browser
 */
/**
 * Table to display a set of course_types.
 */
class AdminRequestTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_REQUEST;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);
        return $actions;
    }
}
