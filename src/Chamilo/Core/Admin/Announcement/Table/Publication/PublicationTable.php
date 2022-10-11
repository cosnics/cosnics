<?php
namespace Chamilo\Core\Admin\Announcement\Table\Publication;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Admin\Announcement\Service\RightsService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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
     * @var \Chamilo\Core\Admin\Announcement\Service\RightsService
     */
    private $rightsService;

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
     * @param \Chamilo\Core\Admin\Announcement\Service\RightsService $rightsService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     *
     * @throws \Exception
     */
    public function __construct(
        $component, PublicationService $publicationService, RightsService $rightsService, UserService $userService,
        GroupService $groupService
    )
    {
        parent::__construct($component);

        $this->publicationService = $publicationService;
        $this->rightsService = $rightsService;
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
     * @return \Chamilo\Core\Admin\Announcement\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
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
     * Gets the table's cell renderer or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\TableCellRenderer
     */
    public function get_cell_renderer()
    {
        if (!isset($this->cellRenderer))
        {
            $this->cellRenderer = new PublicationTableCellRenderer(
                $this, $this->getRightsService(), $this->getUserService(), $this->getGroupService()
            );
        }

        return $this->cellRenderer;
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
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function get_implemented_form_actions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->get_component()->get_user()->is_platform_admin())
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)),
                    Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)
                )
            );
        }

        return $actions;
    }
}
