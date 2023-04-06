<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the CourseGroupService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceTest extends ChamiloTestCase
{
    /**
     *
     * @var CourseGroupRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupRepositoryMock;

    /**
     * @var CourseGroupDecoratorsManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupDecoratorsManagerMock;

    /**
     * @var CourseGroupService
     */
    protected $courseGroupService;

    /**
     * @var UserService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userServiceMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->courseGroupRepositoryMock = $this->getMockBuilder(CourseGroupRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupDecoratorsManagerMock = $this->getMockBuilder(CourseGroupDecoratorsManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->userServiceMock = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupService = new CourseGroupService(
            $this->courseGroupRepositoryMock, $this->courseGroupDecoratorsManagerMock, $this->userServiceMock
        );
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->courseGroupRepositoryMock);
        unset($this->courseGroupDecoratorsManagerMock);
        unset($this->userServiceMock);
        unset($this->courseGroupService);
    }

    public function testCountCourseGroupsInCourse()
    {
        $this->courseGroupRepositoryMock->expects($this->once())
            ->method('countCourseGroupsInCourse')
            ->with(5)
            ->will($this->returnValue(140));

        $this->assertEquals(140, $this->courseGroupService->countCourseGroupsInCourse(5));
    }
}

