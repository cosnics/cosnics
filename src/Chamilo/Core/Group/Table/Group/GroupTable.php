<?php
namespace Chamilo\Core\Group\Table\Group;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GroupTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_GROUP_ID;

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
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_GROUP)), 
                Translation::get('RemoveSelected', null, Utilities::COMMON_LIBRARIES)));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_TRUNCATE_GROUP)), 
                Translation::get('TruncateSelected')));
        return $actions;
    }
}
