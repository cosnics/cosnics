<?php

namespace Chamilo\Core\User\Roles\Test\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Service\RoleService;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the Chamilo\Core\User\Roles\Service\RoleService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RoleServiceTest extends ChamiloTestCase
{
    /**
     * @var RoleRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $roleRepositoryMock;

    /**
     * Subject Under Test
     *
     * @var RoleServiceInterface
     */
    protected $roleService;

    public function setUp()
    {
        $this->roleRepositoryMock = $this->getMockForAbstractClass(
            'Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface'
        );

        $this->roleService = new RoleService($this->roleRepositoryMock);
    }

    public function tearDown()
    {
        unset($this->roleRepositoryMock);
        unset($this->roleService);
    }

    public function testCreateRoleByName()
    {
        $roleName = 'ROLE_TEST_USER';

        $this->roleRepositoryMock->expects($this->once())
            ->method('create')
            ->with($this->callback(
                function ($role) use($roleName)
                {
                    return $role instanceof Role && $role->getRole() == $roleName;
                }
            ))
            ->will($this->returnValue(true));

        $this->roleService->createRoleByName($roleName);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateRoleByNameFails()
    {
        $roleName = 'ROLE_TEST_USER';

        $this->roleRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(false));

        $this->roleService->createRoleByName($roleName);
    }

    public function testDeleteRole()
    {
        $role = new Role();

        $this->roleRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($role)
            ->will($this->returnValue(true));

        $this->roleService->deleteRole($role);
    }

    /**
     * @expectedException \Exception
     */
    public function testDeleteRoleFails()
    {
        $role = new Role();

        $this->roleRepositoryMock->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(false));

        $this->roleService->deleteRole($role);
    }

    public function testGetRoleByName()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setRole($roleName);

        $this->mockFindRoleByName($roleName, $role);

        $this->assertEquals($role, $this->roleService->getRoleByName($roleName));
    }

    /**
     * Tests the getRoleByName with a name for a role that does not exist
     *
     * @expectedException \Exception
     */
    public function testGetRoleByNameWithInvalidName()
    {
        $roleName = 'ROLE_TEST_USER';
        $this->mockFindRoleByName($roleName, null);

        $this->roleService->getRoleByName($roleName);
    }

    public function testGetOrCreateRoleByName()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setRole($roleName);

        $this->mockFindRoleByName($roleName, $role);

        $this->assertEquals($role, $this->roleService->getOrCreateRoleByName($roleName));
    }

    /**
     * Tests the getOrCreateRoleByName with a name for a role that does not yet exists
     */
    public function testGetOrCreateRoleByNameWithNewRole()
    {
        $roleName = 'ROLE_TEST_USER';

        $this->mockFindRoleByName($roleName, null);

        $this->roleRepositoryMock->expects($this->once())
            ->method('create')
            ->with($this->callback(
                function ($role) use($roleName)
                {
                    return $role instanceof Role && $role->getRole() == $roleName;
                }
            ))
            ->will($this->returnValue(true));

        $this->roleService->getOrCreateRoleByName($roleName);
    }

    /**
     * Tests the getOrCreateRoleByName with a name for a role that does not yet exists and the create fails
     *
     * @expectedException \Exception
     */
    public function testGetOrCreateRoleByNameWithNewRoleFails()
    {
        $roleName = 'ROLE_TEST_USER';

        $this->mockFindRoleByName($roleName, null);

        $this->roleRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(false));

        $this->roleService->getOrCreateRoleByName($roleName);
    }

    public function testGetRoles()
    {
        $role = new Role();
        $role->setRole('ROLE_TEST_USER');

        $roles = array($role);

        $this->roleRepositoryMock->expects($this->once())
            ->method('findRoles')
            ->will($this->returnValue($roles));

        $this->assertEquals($roles, $this->roleService->getRoles());
    }

    public function testCountRoles()
    {
        $this->roleRepositoryMock->expects($this->once())
            ->method('countRoles')
            ->will($this->returnValue(1));

        $this->assertEquals(1, $this->roleService->countRoles());
    }

    /**
     * @param string $roleName
     * @param Role $returnValue
     */
    protected function mockFindRoleByName($roleName, $returnValue)
    {
        $this->roleRepositoryMock->expects($this->once())
            ->method('findRoleByName')
            ->with($roleName)
            ->will($this->returnValue($returnValue));
    }
}