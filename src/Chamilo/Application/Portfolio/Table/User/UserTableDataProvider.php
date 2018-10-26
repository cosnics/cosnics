<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * Table data provider
 *
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableDataProvider extends DataClassTableDataProvider
{

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     * Constructor
     *
     * @param Table $table
     * @param \Chamilo\Core\User\Service\UserService $mobilityService
     */
    public function __construct($table, UserService $userService)
    {
        parent::__construct($table);
        $this->userService = $userService;
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
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        return $this->getUserService()->findUsers($condition, $offset, $count, $orderProperty);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        return $this->getUserService()->countUsers($condition);
    }
}