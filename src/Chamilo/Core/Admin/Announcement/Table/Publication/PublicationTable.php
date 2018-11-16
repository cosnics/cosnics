<?php
namespace Chamilo\Core\Admin\Announcement\Table\Publication;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Admin\Announcement\Table\Publication
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTable extends RecordTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID;

    /**
     * @var \Chamilo\Core\Admin\Announcement\Service\PublicationService
     */
    private $publicationService;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;

    /**
     * @param $component
     * @param \Chamilo\Core\Admin\Announcement\Service\PublicationService $publicationService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     *
     * @throws \Exception
     */
    public function __construct(
        $component, PublicationService $publicationService, UserService $userService, GroupService $groupService
    )
    {
        parent::__construct($component);

        $this->publicationService = $publicationService;
        $this->userService = $userService;
        $this->groupService = $groupService;
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function setGroupService(GroupService $groupService): void
    {
        $this->groupService = $groupService;
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Service\PublicationService
     */
    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService): void
    {
        $this->publicationService = $publicationService;
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Table::get_data_provider()
     */
    public function get_data_provider()
    {
        if (!isset($this->dataProvider))
        {
            $this->dataProvider = new PublicationTableDataProvider($this, $this->getPublicationService());
        }

        return $this->dataProvider;
    }

    /**
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\TableCellRenderer
     */
    public function get_cell_renderer()
    {
        if (!isset($this->cellRenderer))
        {
            $this->cellRenderer =
                new PublicationTableCellRenderer($this, $this->getUserService(), $this->getGroupService());
        }

        return $this->cellRenderer;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->get_component()->get_user()->is_platform_admin())
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)),
                    Translation::get('RemoveSelected', null, Utilities::COMMON_LIBRARIES)
                )
            );
        }

        return $actions;
    }
}
