<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Application\Portfolio\Favourite\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Application\Portfolio\Service\RightsService;

/**
 * A table which represents all users which have portfolios published
 *
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Service\RightsService
     */
    private $rightsService;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     * @throws \Exception
     */
    public function __construct($component, UserService $userService, RightsService $rightsService)
    {
        parent::__construct($component);

        $this->userService = $userService;
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
     *
     * @return \Chamilo\Application\Portfolio\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->rightsService;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService)
    {
        $this->rightsService = $rightsService;
    }

    /**
     * Returns the implemented form actions
     *
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(Manager::context(), Manager::PARAM_FAVOURITE_USER_ID);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        \Chamilo\Application\Portfolio\Manager::PARAM_ACTION => \Chamilo\Application\Portfolio\Manager::ACTION_BROWSE_FAVOURITES,
                        Manager::PARAM_ACTION => Manager::ACTION_CREATE)),
                Translation::getInstance()->getTranslation('CreateFavourites', null, Manager::context()),
                false));

        return $actions;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Table::get_data_provider()
     */
    public function get_data_provider()
    {
        if (! isset($this->dataProvider))
        {
            $this->dataProvider = new UserTableDataProvider($this, $this->getUserService());
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
        if (! isset($this->cell_renderer))
        {
            $this->cell_renderer = new UserTableCellRenderer($this, $this->getRightsService());
        }

        return $this->cell_renderer;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Table::getData()
     */
    public function getData($offset, $count, $orderColumns, $orderDirections)
    {
        $resultSet = $this->get_data_provider()->retrieve_data(
            $this->get_condition(),
            $offset,
            $count,
            $this->determineOrderProperties($orderColumns, $orderDirections));

        $tableData = array();

        if ($resultSet)
        {
            foreach ($resultSet as $result)
            {
                $this->handle_result($tableData, $result);
            }
        }

        return $tableData;
    }
}