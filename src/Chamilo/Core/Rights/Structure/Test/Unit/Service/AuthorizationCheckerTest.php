<?php
namespace Chamilo\Core\Rights\Structure\Test\Unit\Service;

use Chamilo\Core\Rights\Structure\Service\AuthorizationChecker;
use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationRoleServiceInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the \Chamilo\Core\Rights\Structure\Service\AuthorizationChecker class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AuthorizationCheckerTest extends ChamiloTestCase
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @var UserRoleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userRoleServiceMock;

    /**
     * @var StructureLocationRoleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureLocationRoleServiceMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->userRoleServiceMock =
            $this->getMockForAbstractClass('Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface');

        $this->structureLocationRoleServiceMock = $this->getMockForAbstractClass(
            'Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationRoleServiceInterface'
        );

        $this->authorizationChecker =
            new AuthorizationChecker($this->userRoleServiceMock, $this->structureLocationRoleServiceMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->userRoleServiceMock);
        unset($this->structureLocationRoleService);
        unset($this->authorizationChecker);
    }

    public function testisAuthorized()
    {
        $user = new User();
        $context = 'Application\Weblcms';
        $action = 'ManageCourses';

        $userRoles = array('ROLE_ADMINISTRATOR');

        $this->mockGetRolesForLocationByContextAndAction($context, $action, $userRoles);
        $this->mockDoesUserHasAtLeasOneRole($user, $userRoles);

        $this->assertTrue($this->authorizationChecker->isAuthorized($user, $context, $action));
    }

    public function testIsAuthorizedReturnsTrueWhenNoRolesOnLocation()
    {
        $user = new User();
        $context = 'Application\Weblcms';
        $action = 'ManageCourses';

        $userRoles = [];
        $this->mockGetRolesForLocationByContextAndAction($context, $action, $userRoles);
        $this->assertTrue($this->authorizationChecker->isAuthorized($user, $context, $action));
    }

    public function testIsAuthorizedReturnsFalseWhenNoValidRoles()
    {
        $user = new User();
        $context = 'Application\Weblcms';
        $action = 'ManageCourses';

        $userRoles = array('ROLE_ADMINISTRATOR');

        $this->mockGetRolesForLocationByContextAndAction($context, $action, $userRoles);
        $this->mockDoesUserHasAtLeasOneRole($user, $userRoles, false);

        $this->assertFalse($this->authorizationChecker->isAuthorized($user, $context, $action));
    }

    public function testCheckAuthorization()
    {
        $user = new User();
        $context = 'Application\Weblcms';
        $action = 'ManageCourses';

        $userRoles = array('ROLE_ADMINISTRATOR');

        $this->mockGetRolesForLocationByContextAndAction($context, $action, $userRoles);
        $this->mockDoesUserHasAtLeasOneRole($user, $userRoles);

        $this->authorizationChecker->checkAuthorization($user, $context, $action);
    }

    /**
     * @expectedException \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function testCheckAuthorizationWhenNotAuthorized()
    {
        $user = new User();
        $context = 'Application\Weblcms';
        $action = 'ManageCourses';

        $userRoles = array('ROLE_ADMINISTRATOR');

        $this->mockGetRolesForLocationByContextAndAction($context, $action, $userRoles);
        $this->mockDoesUserHasAtLeasOneRole($user, $userRoles, false);

        $this->authorizationChecker->checkAuthorization($user, $context, $action);
    }

    /**
     * @param string $context
     * @param string $action
     * @param array $userRoles
     */
    protected function mockGetRolesForLocationByContextAndAction($context, $action, $userRoles)
    {
        $this->structureLocationRoleServiceMock->expects($this->once())
            ->method('getRolesForLocationByContextAndAction')
            ->with($context, $action)
            ->will($this->returnValue($userRoles));
    }

    /**
     * @param User $user
     * @param array $userRoles
     * @param bool $returnValue
     */
    protected function mockDoesUserHasAtLeasOneRole($user, $userRoles, $returnValue = true)
    {
        $this->userRoleServiceMock->expects($this->once())
            ->method('doesUserHaveAtLeastOneRole')
            ->with($user, $userRoles)
            ->will($this->returnValue($returnValue));
    }

}