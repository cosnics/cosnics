<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Unit\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\Repository\CourseGroupOffice365ReferenceRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the CourseGroupOffice365ReferenceService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365ReferenceServiceTest extends ChamiloTestCase
{
    /**
     * @var CourseGroupOffice365ReferenceService
     */
    protected $courseGroupOffice365ReferenceService;

    /**
     * @var CourseGroupOffice365ReferenceRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupOffice365ReferenceRepositoryMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->courseGroupOffice365ReferenceRepositoryMock =
            $this->getMockBuilder(CourseGroupOffice365ReferenceRepository::class)
                ->disableOriginalConstructor()->getMock();

        $this->courseGroupOffice365ReferenceService =
            new CourseGroupOffice365ReferenceService($this->courseGroupOffice365ReferenceRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->courseGroupOffice365ReferenceRepositoryMock);
        unset($this->courseGroupOffice365ReferenceService);
    }

    public function testCreateReferenceForCourseGroup()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(5);

        $office365GroupId = 10;

        $this->courseGroupOffice365ReferenceRepositoryMock->expects($this->once())
            ->method('createReference')
            ->with(
                $this->callback(
                    function (CourseGroupOffice365Reference $courseGroupOffice365Reference) {
                        return $courseGroupOffice365Reference->getOffice365GroupId() == 10 &&
                            $courseGroupOffice365Reference->getCourseGroupId() == 5;
                    }
                )
            )
            ->will($this->returnValue(true));

        $this->assertInstanceOf(
            CourseGroupOffice365Reference::class,
            $this->courseGroupOffice365ReferenceService->createReferenceForCourseGroup($courseGroup, $office365GroupId)
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateReferenceForCourseGroupThrowsExceptionOnFalse()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(5);

        $office365GroupId = 10;

        $this->courseGroupOffice365ReferenceRepositoryMock->expects($this->once())
            ->method('createReference')
            ->will($this->returnValue(false));

        $this->courseGroupOffice365ReferenceService->createReferenceForCourseGroup($courseGroup, $office365GroupId);
    }

    public function testCourseGroupHasReference()
    {
        $courseGroup = new CourseGroup();
        $reference = new CourseGroupOffice365Reference();

        $this->courseGroupOffice365ReferenceRepositoryMock->expects($this->once())
            ->method('findByCourseGroup')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->assertTrue($this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup));
    }

    public function testCourseGroupHasReferenceWithoutReference()
    {
        $courseGroup = new CourseGroup();

        $this->courseGroupOffice365ReferenceRepositoryMock->expects($this->once())
            ->method('findByCourseGroup')
            ->with($courseGroup)
            ->will($this->returnValue(null));

        $this->assertFalse($this->courseGroupOffice365ReferenceService->courseGroupHasReference($courseGroup));
    }

    public function testGetCourseGroupReference()
    {
        $courseGroup = new CourseGroup();
        $reference = new CourseGroupOffice365Reference();

        $this->courseGroupOffice365ReferenceRepositoryMock->expects($this->once())
            ->method('findByCourseGroup')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->assertEquals(
            $reference, $this->courseGroupOffice365ReferenceService->getCourseGroupReference($courseGroup)
        );
    }

//    public function testUnlinkCourseGroupReference()
//    {
//        $reference = new CourseGroupOffice365Reference();
//        $reference->setLinked(true);
//
//        $this->courseGroupOffice365ReferenceRepositoryMock->expects($this->once())
//            ->method('updateReference')
//            ->with(
//                $this->callback(
//                    function (CourseGroupOffice365Reference $courseGroupOffice365Reference) {
//                        return !$courseGroupOffice365Reference->isLinked();
//                    }
//                )
//            )
//            ->will($this->returnValue(true));
//
//        $this->courseGroupOffice365ReferenceService->unlinkCourseGroupReference($reference);
//    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLinkCourseGroupReferenceThrowsExceptionOnFalse()
    {
        $reference = new CourseGroupOffice365Reference();
        $reference->setLinked(false);

        $this->courseGroupOffice365ReferenceRepositoryMock->expects($this->once())
            ->method('updateReference')
            ->will($this->returnValue(false));

        $this->courseGroupOffice365ReferenceService->unlinkCourseGroupReference($reference);
    }
}

