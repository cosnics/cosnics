<?php

namespace Chamilo\Core\User\Roles\Test\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Service\UserRoleService;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\DataClass\RoleRelation;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the Chamilo\Core\User\Roles\Service\UserRoleService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserRoleServiceTest extends ChamiloTestCase
{
    /**
     * @var RoleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $roleServiceMock;

    /**
     * @var UserRoleRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userRoleRepositoryMock;

    /**
     * Subject under test
     *
     * @var UserRoleService
     */
    protected $userRoleService;

    protected function setUp(): void    {
        $this->roleServiceMock =
            $this->getMockForAbstractClass('Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface');

        $this->userRoleRepositoryMock = $this->getMockForAbstractClass(
            'Chamilo\Core\User\Roles\Storage\Repository\Interfaces\UserRoleRepositoryInterface'
        );

        $this->userRoleService = new UserRoleService($this->roleServiceMock, $this->userRoleRepositoryMock);
    }

    protected function tearDown(): void    {
        unset($this->roleServiceMock);
        unset($this->userRoleRepositoryInterfaceMock);
        unset($this->userRoleService);
    }

    public function testGetRolesForUser()
    {
        $user = new User();
        $user->setId(10);

        $role = new Role();
        $role->setRole('ROLE_TEST_USER');

        $roles = array($role);

        $this->mockFindRolesForUser($user, $roles);

        $this->assertEquals($roles, $this->userRoleService->getRolesForUser($user));
    }

    /**
     * Tests that the getRolesForUser method for a user with no roles attached returns the default role
     */
    public function testGetRolesForUserWithNoAttachedRoles()
    {
        $user = new User();
        $user->setId(10);

        $role = new Role();
        $role->setRole('ROLE_DEFAULT_USER');

        $this->mockFindRolesForUser($user, array());
        $this->mockGetOrCreateRoleByName('ROLE_DEFAULT_USER', $role);

        $this->assertEquals(array($role), $this->userRoleService->getRolesForUser($user));
    }

    /**
     * Tests that the getRolesForUser method for a platform admin adds the role_administrator by default
     */
    public function testGetRolesForUserAsPlatformAdmin()
    {
        $user = new User();
        $user->setId(10);
        $user->set_platformadmin(true);

        $role = new Role();
        $role->setRole('ROLE_TEST_USER');

        $administratorRole = new Role();
        $administratorRole->setRole('ROLE_ADMINISTRATOR');

        $userRoles = array($role);
        $expectedRoles = array($role, $administratorRole);

        $this->mockFindRolesForUser($user, $userRoles);
        $this->mockGetOrCreateRoleByName('ROLE_ADMINISTRATOR', $administratorRole);

        $this->assertEquals($expectedRoles, $this->userRoleService->getRolesForUser($user));
    }

    public function testDoesUserHasAtLeastOneRole()
    {
        $user = new User();
        $user->setId(10);

        $role = new Role();
        $role->setId(2);
        $role->setRole('ROLE_TEST_USER');

        $role2 = new Role();
        $role->setId(8);
        $role2->setRole('ROLE_COUNSELOR');

        $rolesToMatch = array($role, $role2);

        $this->mockFindRolesForUser($user, array($role));

        $this->assertTrue($this->userRoleService->doesUserHasAtLeastOneRole($user, $rolesToMatch));
    }

    public function testDoesUserHasAtLeasOneRoleReturnsFalse()
    {
        $user = new User();
        $user->setId(10);

        $role = new Role();
        $role->setId(2);
        $role->setRole('ROLE_TEST_USER');

        $role2 = new Role();
        $role->setId(8);
        $role2->setRole('ROLE_COUNSELOR');

        $rolesToMatch = array($role2);

        $this->mockFindRolesForUser($user, array($role));

        $this->assertFalse($this->userRoleService->doesUserHasAtLeastOneRole($user, $rolesToMatch));
    }

    public function testDoesUserHasAtLeastOneRoleWithNoRolesToMatch()
    {
        $user = new User();
        $user->setId(10);

        $role = new Role();
        $role->setId(2);
        $role->setRole('ROLE_TEST_USER');

        $this->mockFindRolesForUser($user, array($role));

        $this->assertFalse($this->userRoleService->doesUserHasAtLeastOneRole($user, array()));
    }

    public function testGetUsersForRole()
    {
        $user = new User();
        $user->setId(10);

        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(8);
        $role->setRole($roleName);

        $this->mockGetRoleByName($roleName, $role);

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('findUsersForRole')
            ->with($role->getId())
            ->will($this->returnValue(array($user)));
        
        $this->assertEquals(array($user), $this->userRoleService->getUsersForRole($roleName));
    }

    public function testAddRoleForUser()
    {
        $user = new User();
        $user->setId(10);

        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(8);
        $role->setRole($roleName);

        $this->mockGetOrCreateRoleByName($roleName, $role);

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('create')
            ->with($this->callback(function($userRoleRelation) use($role, $user) {
                return $userRoleRelation instanceof RoleRelation && $userRoleRelation->getRoleId() == $role->getId()
                    && $userRoleRelation->getUserId() == $user->getId();
            }))
            ->will($this->returnValue(true));

        $this->userRoleService->addRoleForUser($user, $roleName);
    }

    /**
     * @expectedException \Exception
     */
    public function testAddRoleForUserThrowsException()
    {
        $user = new User();
        $user->setId(10);

        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(8);
        $role->setRole($roleName);

        $this->mockGetOrCreateRoleByName($roleName, $role);

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue(false));

        $this->userRoleService->addRoleForUser($user, $roleName);
    }

    public function testRemoveRoleFromUser()
    {
        $user = new User();
        $user->setId(10);

        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(8);
        $role->setRole($roleName);

        $this->mockGetRoleByName($roleName, $role);

        $userRoleRelation = new RoleRelation();

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('findUserRoleRelationByRoleAndUser')
            ->with($role->getId(), $user->getId())
            ->will($this->returnValue($userRoleRelation));

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($userRoleRelation)
            ->will($this->returnValue(true));

        $this->userRoleService->removeRoleFromUser($user, $roleName);
    }

    /**
     * Tests that the remove role from user with an invalid role doesn't delete anything
     */
    public function testRemoveRoleFromUserWithInvalidRole()
    {
        $user = new User();
        $user->setId(10);

        $roleName = 'ROLE_TEST_USER';

        $this->roleServiceMock->expects($this->once())
            ->method('getRoleByName')
            ->will($this->throwException(new \Exception()));

        $this->userRoleRepositoryMock->expects($this->never())
            ->method('delete');

        $this->userRoleService->removeRoleFromUser($user, $roleName);
    }

    public function testRemoveRoleThatIsNotAttached()
    {
        $user = new User();
        $user->setId(10);

        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(8);
        $role->setRole($roleName);

        $this->mockGetRoleByName($roleName, $role);

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('findUserRoleRelationByRoleAndUser')
            ->will($this->returnValue(null));

        $this->userRoleRepositoryMock->expects($this->never())
            ->method('delete');

        $this->userRoleService->removeRoleFromUser($user, $roleName);
    }

    /**
     * @expectedException \Exception
     */
    public function testRemoveRoleFromUserFails()
    {
        $user = new User();
        $user->setId(10);

        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(8);
        $role->setRole($roleName);

        $this->mockGetRoleByName($roleName, $role);

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('findUserRoleRelationByRoleAndUser')
            ->will($this->returnValue(new RoleRelation()));

        $this->userRoleRepositoryMock->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(false));

        $this->userRoleService->removeRoleFromUser($user, $roleName);
    }

    /**
     * @param User $user
     * @param $returnValue
     */
    protected function mockFindRolesForUser(User $user, $returnValue)
    {
        $this->userRoleRepositoryMock->expects($this->once())
            ->method('findRolesForUser')
            ->with($user->getId())
            ->will($this->returnValue($returnValue));
    }

    /**
     * @param string $expectedRoleName
     * @param Role $returnRole
     */
    protected function mockGetOrCreateRoleByName($expectedRoleName, Role $returnRole = null)
    {
        if(empty($returnRole))
        {
            $returnRole = new Role();
            $returnRole->setRole($expectedRoleName);
        }

        $this->roleServiceMock->expects($this->once())
            ->method('getOrCreateRoleByName')
            ->with($expectedRoleName)
            ->will($this->returnValue($returnRole));
    }

    /**
     * @param string $roleName
     * @param Role $role
     */
    protected function mockGetRoleByName($roleName, $role)
    {
        $this->roleServiceMock->expects($this->once())
            ->method('getRoleByName')
            ->with($roleName)
            ->will($this->returnValue($role));
    }

}