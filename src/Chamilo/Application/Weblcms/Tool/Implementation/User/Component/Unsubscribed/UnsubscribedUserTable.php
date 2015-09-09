<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * * *************************************************************************** Table to display a list of users not in
 * a course.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedUserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_OBJECTS;

    public function get_implemented_form_actions()
    {
        if (! Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_GROUP))
        {
            // add subscribe options
            $actions = new TableFormActions(__NAMESPACE__);

            //Allowed to subscribe an user
            $actions->add_form_action(
                new TableFormAction(
                    array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_SUBSCRIBE),
                    Translation :: get('SubscribeSelectedAsStudent'),
                    false));

            $actions->add_form_action(
                new TableFormAction(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_SUBSCRIBE_AS_ADMIN),
                    Translation :: get('SubscribeSelectedAsAdmin'),
                    false));

            //Allowed to request to subscribe an user
            $actions->add_form_action(
                new TableFormAction(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_REQUEST_SUBSCRIBE_USERS),
                    Manager :: ACTION_REQUEST_SUBSCRIBE_USERS,
                    Translation::get('RequestUsers'),
                    false
                    )
                );

            return $actions;
        }
    }
}