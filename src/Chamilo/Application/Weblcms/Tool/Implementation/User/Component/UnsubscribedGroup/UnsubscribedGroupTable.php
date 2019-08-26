<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * * *************************************************************************** Table to display a list of groups not
 * in a course.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedGroupTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_OBJECTS;

    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var GroupSubscriptionService
     */
    protected $groupSubscriptionService;

    /**
     * @param Application $parentComponent
     * @param GroupService $groupService
     * @param GroupSubscriptionService $groupSubscriptionService
     *
     * @throws \Exception
     */
    public function __construct(
        Application $parentComponent, GroupService $groupService, GroupSubscriptionService $groupSubscriptionService
    )
    {
        parent::__construct($parentComponent);

        $this->groupService = $groupService;
        $this->groupSubscriptionService = $groupSubscriptionService;
    }

    /**
     * @return GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @return GroupSubscriptionService
     */
    public function getGroupSubscriptionService(): GroupSubscriptionService
    {
        return $this->groupSubscriptionService;
    }

    public function get_implemented_form_actions()
    {
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            // add subscribe options
            $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
            
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_GROUPS)), 
                    Translation::get('SubscribeSelectedGroups'), 
                    false));
            
            return $actions;
        }
    }
}