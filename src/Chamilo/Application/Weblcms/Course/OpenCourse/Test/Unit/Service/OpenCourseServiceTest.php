<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Test\Unit\Service;

use Chamilo\Application\Weblcms\Course\OpenCourse\Service\Interfaces\OpenCourseServiceInterface;
use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\OpenCourse\Storage\Repository\Interfaces\OpenCourseRepositoryInterface;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Exception;

/**
 * Tests the Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseServiceTest extends ChamiloTestCase
{

    /**
     *
     * @var OpenCourseRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $openCourseRepositoryMock;

    /**
     *
     * @var AuthorizationCheckerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationCheckerMock;

    /**
     *
     * @var UserRoleServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userRoleServiceMock;

    /**
     * Subject under test
     *
     * @var OpenCourseServiceInterface
     */
    protected $openCourseService;

    public function setUp()
    {
        $this->openCourseRepositoryMock = $this->getMockForAbstractClass(OpenCourseRepositoryInterface::class);
        $this->authorizationCheckerMock = $this->getMockForAbstractClass(AuthorizationCheckerInterface::class);
        $this->userRoleServiceMock = $this->getMockForAbstractClass(UserRoleServiceInterface::class);

        $this->openCourseService = new OpenCourseService(
            $this->openCourseRepositoryMock,
            $this->authorizationCheckerMock,
            $this->userRoleServiceMock);
    }

    public function tearDown()
    {
        unset($this->openCourseRepositoryMock);
        unset($this->authorizationCheckerMock);
        unset($this->userRoleServiceMock);
        unset($this->openCourseService);
    }

    public function testGetOpenCourses()
    {
        $user = new User();
        $context = 'Chamilo\Application\Weblcms\Course\OpenCourse';
        $action = 'ManageOpenCourses';
        $iterator = new DataClassIterator(Course::class, [new Course()]);

        $this->authorizationCheckerMock->expects($this->once())->method('isAuthorized')->with($user, $context, $action)->will(
            $this->returnValue(true));

        $this->openCourseRepositoryMock->expects($this->once())->method('findAllOpenCourses')->will(
            $this->returnValue($iterator));

        $this->assertEquals($iterator, $this->openCourseService->getOpenCourses($user));
    }

    /**
     * Tests the getOpenCourses when you are no manager for open courses
     */
    public function testGetOpenCoursesNoManager()
    {
        $user = new User();
        $iterator = new DataClassIterator(Course::class, [new Course()]);
        $roles = array(new Role());

        $this->authorizationCheckerMock->expects($this->once())->method('isAuthorized')->will($this->returnValue(false));

        $this->openCourseRepositoryMock->expects($this->never())->method('findAllOpenCourses');

        $this->userRoleServiceMock->expects($this->once())->method('getRolesForUser')->with($user)->will(
            $this->returnValue($roles));

        $this->openCourseRepositoryMock->expects($this->once())->method('findOpenCoursesByRoles')->with($roles)->will(
            $this->returnValue($iterator));

        $this->assertEquals($iterator, $this->openCourseService->getOpenCourses($user));
    }

    public function testGetClosedCourses()
    {
        $iterator = new DataClassIterator(Course::class, [new Course()]);

        $this->openCourseRepositoryMock->expects($this->once())->method('findClosedCourses')->will(
            $this->returnValue($iterator));

        $this->assertEquals($iterator, $this->openCourseService->getClosedCourses());
    }

    public function testCountOpenCourses()
    {
        $user = new User();
        $context = 'Chamilo\Application\Weblcms\Course\OpenCourse';
        $action = 'ManageOpenCourses';

        $fakeCount = 5;

        $this->authorizationCheckerMock->expects($this->once())->method('isAuthorized')->with($user, $context, $action)->will(
            $this->returnValue(true));

        $this->openCourseRepositoryMock->expects($this->once())->method('countAllOpenCourses')->will(
            $this->returnValue($fakeCount));

        $this->assertEquals($fakeCount, $this->openCourseService->countOpenCourses($user));
    }

    /**
     * Tests the countOpenCourses when you are no manager for open courses
     */
    public function testCountOpenCoursesNoManager()
    {
        $user = new User();
        $roles = array(new Role());

        $this->authorizationCheckerMock->expects($this->once())->method('isAuthorized')->will($this->returnValue(false));

        $this->openCourseRepositoryMock->expects($this->never())->method('countAllOpenCourses');

        $this->userRoleServiceMock->expects($this->once())->method('getRolesForUser')->with($user)->will(
            $this->returnValue($roles));

        $fakeCount = 5;

        $this->openCourseRepositoryMock->expects($this->once())->method('countOpenCoursesByRoles')->with($roles)->will(
            $this->returnValue($fakeCount));

        $this->assertEquals($fakeCount, $this->openCourseService->countOpenCourses($user));
    }

    public function testCountClosedCourses()
    {
        $fakeCount = 5;

        $this->openCourseRepositoryMock->expects($this->once())->method('countClosedCourses')->will(
            $this->returnValue($fakeCount));

        $this->assertEquals($fakeCount, $this->openCourseService->countClosedCourses());
    }

    public function testGetRolesForOpenCourse()
    {
        $course = new Course();
        $resultSet = new ArrayResultSet(array(new Role()));

        $this->openCourseRepositoryMock->expects($this->once())->method('getRolesForOpenCourse')->with($course)->will(
            $this->returnValue($resultSet));

        $this->assertEquals($resultSet, $this->openCourseService->getRolesForOpenCourse($course));
    }

    public function testAttachRolesToCoursesByIds()
    {
        $user = new User();

        $roleIds = array(3, 5);
        $courseIds = array(2, 4);

        $context = 'Chamilo\Application\Weblcms\Course\OpenCourse';
        $action = 'ManageOpenCourses';

        $this->authorizationCheckerMock->expects($this->once())->method('checkAuthorization')->with(
            $user,
            $context,
            $action);

        $this->openCourseRepositoryMock->expects($this->exactly(4))->method('create')->withConsecutive(
            $this->callback(
                function ($courseEntityRelation)
                {
                    return $courseEntityRelation instanceof CourseEntityRelation &&
                         $courseEntityRelation->getEntityType() == CourseEntityRelation::ENTITY_TYPE_ROLE &&
                         $courseEntityRelation->getEntityId() == 3 && $courseEntityRelation->get_course_id() == 2;
                }))->will($this->returnValue(true));

        $this->openCourseService->attachRolesToCoursesByIds($user, $courseIds, $roleIds);
    }

    public function testAttachRolesToCoursesByIdsWithEmptyRoleIds()
    {
        $user = new User();

        $this->openCourseRepositoryMock->expects($this->never())->method('create');

        $this->openCourseService->attachRolesToCoursesByIds($user, array(2, 4), array());
    }

    public function testAttachRolesToCoursesByIdsWithEmptyCourseIds()
    {
        $user = new User();

        $this->openCourseRepositoryMock->expects($this->never())->method('create');

        $this->openCourseService->attachRolesToCoursesByIds($user, array(), array(3, 5));
    }

    /**
     * @expectedException \Exception
     */
    public function testAttachRolesToCoursesByIdsFails()
    {
        $user = new User();

        $this->openCourseRepositoryMock->expects($this->once())->method('create')->will($this->returnValue(false));

        $this->openCourseService->attachRolesToCoursesByIds($user, array(2), array(1));
    }

    /**
     * Tests that the attachRolesToCoursesByIds function throws an exception if you are no manager for open courses
     * @expectedException \Exception
     */
    public function testAttachRolesToCoursesByIdsNoManager()
    {
        $this->authorizationCheckerMock->expects($this->once())->method('checkAuthorization')->will(
            $this->throwException(new Exception()));

        $this->openCourseService->attachRolesToCoursesByIds(new User(), array(2), array(1));
    }

    public function testRemoveCoursesAsOpenCourse()
    {
        $user = new User();

        $courseIds = array(2, 4);

        $context = 'Chamilo\Application\Weblcms\Course\OpenCourse';
        $action = 'ManageOpenCourses';

        $this->authorizationCheckerMock->expects($this->once())->method('checkAuthorization')->with(
            $user,
            $context,
            $action);

        $this->openCourseRepositoryMock->expects($this->once())->method('removeCoursesAsOpenCourse')->with($courseIds)->will(
            $this->returnValue(true));

        $this->openCourseService->removeCoursesAsOpenCourse($user, $courseIds);
    }

    /**
     * Tests that the removeCoursesAsOpenCourse function throws an exception if you are no manager for open courses
     * @expectedException \Exception
     */
    public function testRemoveCoursesAsOpenCourseNoManager()
    {
        $this->authorizationCheckerMock->expects($this->once())->method('checkAuthorization')->will(
            $this->throwException(new Exception()));

        $this->openCourseService->removeCoursesAsOpenCourse(new User(), array(2));
    }

    /**
     * @expectedException \Exception
     */
    public function testRemoveCoursesAsOpenCourseFails()
    {
        $this->openCourseRepositoryMock->expects($this->once())->method('removeCoursesAsOpenCourse')->will(
            $this->returnValue(false));

        $this->openCourseService->removeCoursesAsOpenCourse(new User(), array(2, 4));
    }

    public function testUpdateRolesForCourses()
    {
        $user = new User();

        $courseIds = array(2, 4);
        $roleIds = array(3, 5);

        $context = 'Chamilo\Application\Weblcms\Course\OpenCourse';
        $action = 'ManageOpenCourses';

        $this->authorizationCheckerMock->expects($this->exactly(2))->method('checkAuthorization')->with(
            $user,
            $context,
            $action);

        $this->openCourseRepositoryMock->expects($this->once())->method('removeCoursesAsOpenCourse')->with($courseIds)->will(
            $this->returnValue(true));

        $this->openCourseRepositoryMock->expects($this->exactly(4))->method('create')->withConsecutive(
            $this->callback(
                function ($courseEntityRelation)
                {
                    return $courseEntityRelation instanceof CourseEntityRelation &&
                         $courseEntityRelation->getEntityType() == CourseEntityRelation::ENTITY_TYPE_ROLE &&
                         $courseEntityRelation->getEntityId() == 3 && $courseEntityRelation->get_course_id() == 2;
                }))->will($this->returnValue(true));

        $this->openCourseService->updateRolesForCourses($user, $courseIds, $roleIds);
    }

    public function testIsCourseOpenForUser()
    {
        $user = new User();
        $course = new Course();

        $roles = array(new Role());
        $resultSet = new ArrayResultSet($roles);

        $this->openCourseRepositoryMock->expects($this->once())->method('getRolesForOpenCourse')->with($course)->will(
            $this->returnValue($resultSet));

        $this->userRoleServiceMock->expects($this->once())->method('doesUserHasAtLeastOneRole')->with($user, $roles)->will(
            $this->returnValue(true));

        $this->assertTrue($this->openCourseService->isCourseOpenForUser($course, $user));
    }

    public function testIsCourseOpenForUserReturnsFalse()
    {
        $resultSet = new ArrayResultSet(array(new Role()));

        $this->openCourseRepositoryMock->expects($this->once())->method('getRolesForOpenCourse')->will(
            $this->returnValue($resultSet));

        $this->userRoleServiceMock->expects($this->once())->method('doesUserHasAtLeastOneRole')->will(
            $this->returnValue(false));

        $this->assertFalse($this->openCourseService->isCourseOpenForUser(new Course(), new User()));
    }
}