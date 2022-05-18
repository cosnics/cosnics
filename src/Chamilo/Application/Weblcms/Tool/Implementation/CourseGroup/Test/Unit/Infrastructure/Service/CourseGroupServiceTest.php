<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepositoryInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
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
     * @var CourseGroupRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupRepositoryMock;

    /**
     * @var CourseGroupService
     */
    protected $courseGroupService;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->courseGroupRepositoryMock = $this->getMockBuilder(CourseGroupRepositoryInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupService = new CourseGroupService($this->courseGroupRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->courseGroupRepositoryMock);
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

