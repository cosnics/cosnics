<?php
namespace Chamilo\Core\Rights\Structure\Test\Unit\Service;

use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationRoleServiceInterface;
use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationServiceInterface;
use Chamilo\Core\Rights\Structure\Service\StructureLocationRoleService;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocationRole;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRoleRepositoryInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Exception;

/**
 * Tests the Chamilo\Core\Rights\Structure\Service\StructureLocationRoleService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationRoleServiceTest extends ChamiloTestCase
{
    /**
     * @var RoleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $roleServiceMock;

    /**
     * @var StructureLocationServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureLocationServiceMock;

    /**
     * @var StructureLocationRoleRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureLocationRoleRepositoryMock;

    /**
     * @var StructureLocationRoleServiceInterface
     */
    protected $structureLocationRoleService;

    public function setUp(): void
    {
        $this->roleServiceMock =
            $this->getMockForAbstractClass('Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface');

        $this->structureLocationServiceMock = $this->getMockForAbstractClass(
            'Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationServiceInterface'
        );

        $this->structureLocationRoleRepositoryMock = $this->getMockForAbstractClass(
            'Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRoleRepositoryInterface'
        );

        $this->structureLocationRoleService = new StructureLocationRoleService(
            $this->roleServiceMock, $this->structureLocationServiceMock, $this->structureLocationRoleRepositoryMock
        );
    }

    public function tearDown(): void
    {
        unset($this->roleServiceMock);
        unset($this->structureLocationServiceMock);
        unset($this->structureLocationRoleRepositoryMock);
        unset($this->structureLocationRoleService);
    }

    public function testAddRoleToStructureLocation()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(9);

        $structureLocation = new StructureLocation();
        $structureLocation->setId(5);

        $this->roleServiceMock->expects($this->once())->method('getOrCreateRoleByName')->with($roleName)->will(
                $this->returnValue($role)
            );

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method('create')->with(
                $this->callback(
                    function ($structureLocationRole) use ($role, $structureLocation) {
                        return $structureLocationRole instanceof StructureLocationRole &&
                            $structureLocationRole->getRoleId() == $role->getId() &&
                            $structureLocationRole->getStructureLocationId() == $structureLocation->getId();
                    }
                )
            )->will($this->returnValue(true));

        $this->structureLocationRoleService->addRoleToStructureLocation($structureLocation, $roleName);
    }

    /**
     * @expectedException \Exception
     */
    public function testAddRoleToStructureLocationFails()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(9);

        $structureLocation = new StructureLocation();
        $structureLocation->setId(5);

        $this->roleServiceMock->expects($this->once())->method('getOrCreateRoleByName')->will(
                $this->returnValue($role)
            );

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method('create')->will(
                $this->returnValue(false)
            );

        $this->structureLocationRoleService->addRoleToStructureLocation($structureLocation, $roleName);
    }

    public function testGetRolesForLocationByContextAndAction()
    {
        $context = 'Chamilo\Application\Weblcms';
        $action = 'ManagePersonalCourses';

        $role = new Role();
        $role->setId(9);

        $structureLocation = new StructureLocation();
        $structureLocation->setId(5);

        $this->structureLocationServiceMock->expects($this->once())->method('getStructureLocationByContextAndAction')
            ->with($context, $action)->will($this->returnValue($structureLocation));

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method('findRolesForStructureLocation')
            ->with($structureLocation->getId())->will($this->returnValue(array($role)));

        $this->assertEquals(
            array($role), $this->structureLocationRoleService->getRolesForLocationByContextAndAction($context, $action)
        );
    }

    public function testGetRolesForLocationByContextAndActionForInvalidStructureLocation()
    {
        $context = 'Chamilo\Application\Weblcms';
        $action = 'ManagePersonalCourses';

        $this->structureLocationServiceMock->expects($this->once())->method('getStructureLocationByContextAndAction')
            ->with($context, $action)->will($this->throwException(new Exception()));

        $this->assertEquals(
            [], $this->structureLocationRoleService->getRolesForLocationByContextAndAction($context, $action)
        );
    }

    public function testRemoveRoleFromStructureLocation()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(9);

        $structureLocation = new StructureLocation();
        $structureLocation->setId(5);

        $structureLocationRole = new StructureLocationRole();

        $this->roleServiceMock->expects($this->once())->method('getRoleByName')->with($roleName)->will(
                $this->returnValue($role)
            );

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method(
                'findStructureLocationRoleByStructureLocationAndRole'
            )->with($structureLocation->getId(), $role->getId())->will($this->returnValue($structureLocationRole));

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method('delete')->with(
                $structureLocationRole
            )->will($this->returnValue(true));

        $this->structureLocationRoleService->removeRoleFromStructureLocation($structureLocation, $roleName);
    }

    /**
     * Tests that the removeRoleFromStructureLocation throws an exception when the system could not remove the role
     *
     * @expectedException \Exception
     */
    public function testRemoveRoleFromStructureLocationFails()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(9);

        $structureLocation = new StructureLocation();
        $structureLocation->setId(5);

        $structureLocationRole = new StructureLocationRole();

        $this->roleServiceMock->expects($this->once())->method('getRoleByName')->with($roleName)->will(
                $this->returnValue($role)
            );

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method(
                'findStructureLocationRoleByStructureLocationAndRole'
            )->will($this->returnValue($structureLocationRole));

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method('delete')->will(
                $this->returnValue(false)
            );

        $this->structureLocationRoleService->removeRoleFromStructureLocation($structureLocation, $roleName);
    }

    /**
     * Tests that the removeRoleFromStructureLocation function doesn't delete when the role is not attached to a
     * structure location
     */
    public function testRemoveRoleFromStructureLocationWhenNotAttached()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(9);

        $structureLocation = new StructureLocation();
        $structureLocation->setId(5);

        $this->roleServiceMock->expects($this->once())->method('getRoleByName')->will($this->returnValue($role));

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method(
                'findStructureLocationRoleByStructureLocationAndRole'
            )->will($this->returnValue(null));

        $this->structureLocationRoleRepositoryMock->expects($this->never())->method('delete');

        $this->structureLocationRoleService->removeRoleFromStructureLocation($structureLocation, $roleName);
    }

    //    public function testGetRolesForLocation()
    //    {
    //        $role = new Role();
    //        $role->setId(9);
    //
    //        $structureLocation = new StructureLocation();
    //        $structureLocation->setId(5);
    //
    //        $this->structureLocationRoleRepositoryMock->expects($this->once())
    //            ->method('findRolesForStructureLocation')
    //            ->with($structureLocation->getId())
    //            ->will($this->returnValue(array($role)));
    //
    //        $this->assertEquals(array($role), $this->structureLocationRoleService->getRolesForLocation($structureLocation));
    //    }

    /**
     * Tests the removeRoleFromStructureLocation with an invalid role name (role does not exist)
     */
    public function testRemoveRoleFromStructureLocationWithInvalidRoleName()
    {
        $this->roleServiceMock->expects($this->once())->method('getRoleByName')->will(
                $this->throwException(new Exception())
            );

        $this->structureLocationRoleService->removeRoleFromStructureLocation(new StructureLocation(), 'ROLE_TEST_USER');
    }

    public function testRemoveRoleFromStructureLocationWithInvalidRoleObject()
    {
        $roleName = 'ROLE_TEST_USER';

        $role = new Role();
        $role->setId(9);

        $structureLocation = new StructureLocation();
        $structureLocation->setId(5);

        $this->roleServiceMock->expects($this->once())->method('getRoleByName')->with($roleName)->will(
                $this->returnValue($role)
            );

        $this->structureLocationRoleRepositoryMock->expects($this->once())->method(
                'findStructureLocationRoleByStructureLocationAndRole'
            )->with($structureLocation->getId(), $role->getId())->will($this->returnValue($structureLocation));

        $this->structureLocationRoleRepositoryMock->expects($this->never())->method('delete');

        $this->structureLocationRoleService->removeRoleFromStructureLocation($structureLocation, $roleName);
    }

}