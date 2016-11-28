<?php

namespace Chamilo\Core\User\Roles\Test\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Service\UserRoleService;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the Chamilo\Core\User\Roles\Service\UserRoleService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRoleServiceTest extends Test
{
    /**
     * @var RoleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $roleServiceMock;

    /**
     * @var UserRoleRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userRoleRepositoryInterfaceMock;

    /**
     * Subject under test
     *
     * @var UserRoleService
     */
    protected $userRoleService;

    public function setUp()
    {
        $this->roleServiceMock =
            $this->getMockForAbstractClass('Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface');

        $this->userRoleRepositoryInterfaceMock = $this->getMockForAbstractClass(
            'Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface'
        );

        $this->userRoleService = new UserRoleService($this->roleServiceMock, $this->userRoleRepositoryInterfaceMock);
    }

    public function tearDown()
    {
        unset($this->roleServiceMock);
        unset($this->userRoleRepositoryInterfaceMock);
        unset($this->userRoleService);
    }


}