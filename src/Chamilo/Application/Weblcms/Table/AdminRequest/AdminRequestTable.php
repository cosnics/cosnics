<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.weblcms_manager.component.admin_request_browser
 */
/**
 * Table to display a set of course_types.
 */
class AdminRequestTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_REQUEST;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_COURSE_USER_SUBSCRIPTION_REQUEST_GRANT)),
                Translation::getInstance()->getTranslation('GrantSelected', null, StringUtilities::LIBRARIES),
                false));

        return $actions;
    }
}
