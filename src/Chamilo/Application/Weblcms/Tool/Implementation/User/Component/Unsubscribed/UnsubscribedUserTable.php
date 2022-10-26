<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * * *************************************************************************** Table to display a list of users not in
 * a course.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedUserTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    public function getTableActions(): TableActions
    {
        $translator = Translation::getInstance();
        if (! Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP))
        {
            // add subscribe options
            $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
            
            // Allowed to subscribe an user
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE)), 
                    $translator->getTranslation('SubscribeSelectedAsStudent', null, Manager::context()), 
                    false));
            
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_AS_ADMIN)), 
                    $translator->getTranslation('SubscribeSelectedAsAdmin', null, Manager::context()), 
                    false));
            
            // Allowed to request to subscribe an user
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_REQUEST_SUBSCRIBE_USERS)), 
                    $translator->getTranslation('RequestUsers', null, Manager::context()), 
                    false));
            
            return $actions;
        }
    }
}