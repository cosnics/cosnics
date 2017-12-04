<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupPublicationCategory;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupPublicationCategoryRepository;
use Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;

/**
 * Tests the CourseGroupPublicationCategoryService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupPublicationCategoryServiceTest extends ChamiloTestCase
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupPublicationCategoryRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupPublicationCategoryRepositoryMock;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $publicationRepositoryMock;

    /**
     * The Weblcms Rights Service
     *
     * @var \Chamilo\Application\Weblcms\Rights\WeblcmsRights | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $weblcmsRightsMock;

    /**
     * @var CourseGroupPublicationCategoryService
     */
    protected $courseGroupPublicationCategoryService;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->courseGroupPublicationCategoryRepositoryMock =
            $this->getMockBuilder(CourseGroupPublicationCategoryRepository::class)
                ->disableOriginalConstructor()->getMock();

        $this->publicationRepositoryMock = $this->getMockBuilder(PublicationRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->weblcmsRightsMock = $this->getMockBuilder(WeblcmsRights::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupPublicationCategoryService = new CourseGroupPublicationCategoryService(
            $this->courseGroupPublicationCategoryRepositoryMock, $this->publicationRepositoryMock, $this->weblcmsRightsMock
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->courseGroupPublicationCategoryRepositoryMock);
        unset($this->publicationRepositoryMock);
        unset($this->weblcmsRightsMock);
        unset($this->courseGroupPublicationCategoryService);
    }

    public function testCreatePublicationCategoryForCourseGroup()
    {
        $groupName = 'TestGroup 101';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(0))
            ->method('create')
            ->with(
                $this->callback(
                    (function (ContentObjectPublicationCategory $contentObjectPublicationCategory) use (
                        $groupName, $toolName
                    ) {
                        return $contentObjectPublicationCategory->get_course() == 5 &&
                            $contentObjectPublicationCategory->get_name() == $groupName &&
                            $contentObjectPublicationCategory->get_tool() == $toolName;
                    })
                )
            )
            ->will($this->returnValue(true));

        $rightsLocation = new RightsLocation();
        $rightsLocation->setId(10);
        $rightsLocation->inherit();

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue($rightsLocation));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(1))
            ->method('update')
            ->with($rightsLocation)
            ->will($this->returnValue(true));

        $this->weblcmsRightsMock->expects($this->exactly(3))
            ->method('set_location_entity_right')
            ->will($this->returnValue(true));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(2))
            ->method('create')
            ->with(
                $this->callback(
                    (function (CourseGroupPublicationCategory $courseGroupPublicationCategory) {
                        return $courseGroupPublicationCategory->getCourseGroupId() == 8;
                    })
                )
            )
            ->will($this->returnValue(true));

        $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreatePublicationCategoryForCourseGroupPublicationNotCreated()
    {
        $groupName = 'TestGroup 101';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue(false));

        $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreatePublicationCategoryForCourseGroupNoLocationFound()
    {
        $groupName = 'TestGroup 101';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue(true));

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue(null));

        $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreatePublicationCategoryForCourseGroupLocationUpdateFailed()
    {
        $groupName = 'TestGroup 101';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue(true));

        $rightsLocation = new RightsLocation();
        $rightsLocation->setId(10);
        $rightsLocation->inherit();

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue($rightsLocation));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(1))
            ->method('update')
            ->with($rightsLocation)
            ->will($this->returnValue(false));

        $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreatePublicationCategoryForCourseGroupSetRightsFailed()
    {
        $groupName = 'TestGroup 101';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue(true));

        $rightsLocation = new RightsLocation();
        $rightsLocation->setId(10);
        $rightsLocation->inherit();

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue($rightsLocation));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(1))
            ->method('update')
            ->with($rightsLocation)
            ->will($this->returnValue(true));

        $this->weblcmsRightsMock->expects($this->exactly(1))
            ->method('set_location_entity_right')
            ->will($this->returnValue(false));

        $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    /**
     * @expectedException \Exception
     */
    public function testCreatePublicationCategoryForCourseGroupCourseGroupPublicationCategoryCreationFailed()
    {
        $groupName = 'TestGroup 101';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue(true));

        $rightsLocation = new RightsLocation();
        $rightsLocation->setId(10);
        $rightsLocation->inherit();

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue($rightsLocation));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(1))
            ->method('update')
            ->with($rightsLocation)
            ->will($this->returnValue(true));

        $this->weblcmsRightsMock->expects($this->exactly(3))
            ->method('set_location_entity_right')
            ->will($this->returnValue(true));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(2))
            ->method('create')
            ->will($this->returnValue(false));

        $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    public function testUpdatePublicationCategoryForCourseGroup()
    {
        $groupName = 'TestGroup 102';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $contentObjectPublicationCategory = new ContentObjectPublicationCategory();
        $contentObjectPublicationCategory->set_name('TestGroup 101');

        $publicationCategories = [$contentObjectPublicationCategory];

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('findPublicationCategoriesForCourseGroup')
            ->with($courseGroup, $toolName)
            ->will($this->returnValue($publicationCategories));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('update')
            ->with($contentObjectPublicationCategory)
            ->will($this->returnValue(true));

        $this->courseGroupPublicationCategoryService->updatePublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    public function testUpdatePublicationCategoryForCourseGroupNoNameChange()
    {
        $groupName = 'TestGroup 101';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $contentObjectPublicationCategory = new ContentObjectPublicationCategory();
        $contentObjectPublicationCategory->set_name('TestGroup 101');

        $publicationCategories = [$contentObjectPublicationCategory];

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('findPublicationCategoriesForCourseGroup')
            ->with($courseGroup, $toolName)
            ->will($this->returnValue($publicationCategories));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->never())
            ->method('update');

        $this->courseGroupPublicationCategoryService->updatePublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    public function testCreateOrUpdatePublicationCategoryForCourseGroup()
    {
        $groupName = 'TestGroup 102';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $contentObjectPublicationCategory = new ContentObjectPublicationCategory();
        $contentObjectPublicationCategory->set_name('TestGroup 101');

        $publicationCategories = [$contentObjectPublicationCategory];

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->exactly(2))
            ->method('findPublicationCategoriesForCourseGroup')
            ->with($courseGroup, $toolName)
            ->will($this->returnValue($publicationCategories));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('update')
            ->with($contentObjectPublicationCategory)
            ->will($this->returnValue(true));

        $this->courseGroupPublicationCategoryService->createOrUpdatePublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    public function testCreateOrUpdatePublicationCategoryForCourseGroupWithCreate()
    {
        $groupName = 'TestGroup 102';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('findPublicationCategoriesForCourseGroup')
            ->will($this->returnValue(null));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue(true));

        $rightsLocation = new RightsLocation();
        $rightsLocation->setId(10);
        $rightsLocation->inherit();

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue($rightsLocation));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(2))
            ->method('update')
            ->will($this->returnValue(true));

        $this->weblcmsRightsMock->expects($this->exactly(3))
            ->method('set_location_entity_right')
            ->will($this->returnValue(true));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->at(3))
            ->method('create')
            ->will($this->returnValue(true));

        $this->courseGroupPublicationCategoryService->createOrUpdatePublicationCategoryForCourseGroup($courseGroup, $toolName);
    }

    public function testDisconnectPublicationCategoryFromCourseGroup()
    {
        $groupName = 'TestGroup 102';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $contentObjectPublicationCategory = new ContentObjectPublicationCategory();
        $contentObjectPublicationCategory->set_name('TestGroup 101');

        $publicationCategories = [$contentObjectPublicationCategory];

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('findPublicationCategoriesForCourseGroup')
            ->with($courseGroup, $toolName)
            ->will($this->returnValue($publicationCategories));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('update')
            ->with($this->callback(function(ContentObjectPublicationCategory $contentObjectPublicationCategory) {
                return $contentObjectPublicationCategory->get_allow_change() == 1;
            }))
            ->will($this->returnValue(true));

        $rightsLocation = new RightsLocation();
        $rightsLocation->setId(10);
        $rightsLocation->inherit();

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue($rightsLocation));

        $this->weblcmsRightsMock->expects($this->exactly(3))
            ->method('unset_location_entity_right')
            ->will($this->returnValue(true));

        $courseGroupPublicationCategory = new CourseGroupPublicationCategory();

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('findCourseGroupPublicationCategoriesForCourseGroup')
            ->with($courseGroup, $toolName)
            ->will($this->returnValue([$courseGroupPublicationCategory]));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($courseGroupPublicationCategory);

        $this->courseGroupPublicationCategoryService->disconnectPublicationCategoryFromCourseGroup($courseGroup, $toolName);
    }

    /**
     * @expectedException \Exception
     */
    public function testDisconnectPublicationCategoryFromCourseGroupNoLocationFound()
    {
        $groupName = 'TestGroup 102';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $contentObjectPublicationCategory = new ContentObjectPublicationCategory();
        $contentObjectPublicationCategory->set_name('TestGroup 101');

        $publicationCategories = [$contentObjectPublicationCategory];

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('findPublicationCategoriesForCourseGroup')
            ->with($courseGroup, $toolName)
            ->will($this->returnValue($publicationCategories));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('update')
            ->will($this->returnValue(true));

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue(null));

        $this->courseGroupPublicationCategoryService->disconnectPublicationCategoryFromCourseGroup($courseGroup, $toolName);
    }

    /**
     * @expectedException \Exception
     */
    public function testDisconnectPublicationCategoryFromCourseGroupRightsNotUpdated()
    {
        $groupName = 'TestGroup 102';
        $toolName = 'Document';

        $courseGroup = new CourseGroup();
        $courseGroup->set_name($groupName);
        $courseGroup->set_course_code(5);
        $courseGroup->setId(8);

        $contentObjectPublicationCategory = new ContentObjectPublicationCategory();
        $contentObjectPublicationCategory->set_name('TestGroup 101');

        $publicationCategories = [$contentObjectPublicationCategory];

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('findPublicationCategoriesForCourseGroup')
            ->with($courseGroup, $toolName)
            ->will($this->returnValue($publicationCategories));

        $this->courseGroupPublicationCategoryRepositoryMock->expects($this->once())
            ->method('update')
            ->with($this->callback(function(ContentObjectPublicationCategory $contentObjectPublicationCategory) {
                return $contentObjectPublicationCategory->get_allow_change() == 1;
            }))
            ->will($this->returnValue(true));

        $rightsLocation = new RightsLocation();
        $rightsLocation->setId(10);
        $rightsLocation->inherit();

        $this->weblcmsRightsMock->expects($this->once())
            ->method('get_weblcms_location_by_identifier_from_courses_subtree')
            ->will($this->returnValue($rightsLocation));

        $this->weblcmsRightsMock->expects($this->exactly(1))
            ->method('unset_location_entity_right')
            ->will($this->returnValue(false));

        $this->courseGroupPublicationCategoryService->disconnectPublicationCategoryFromCourseGroup($courseGroup, $toolName);
    }


}