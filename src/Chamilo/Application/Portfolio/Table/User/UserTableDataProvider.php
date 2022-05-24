<?php
namespace Chamilo\Application\Portfolio\Table\User;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 *
 * @package Chamilo\Application\Portfolio\Table\User
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
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct($table, UserService $userService)
    {
        parent::__construct($table);
        $this->userService = $userService;
    }

    public function countData(?Condition $condition = null): int
    {
        return $this->getUserService()->countUsers($condition);
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getUserService()->findUsers($condition, $offset, $count, $orderBy);
    }
}