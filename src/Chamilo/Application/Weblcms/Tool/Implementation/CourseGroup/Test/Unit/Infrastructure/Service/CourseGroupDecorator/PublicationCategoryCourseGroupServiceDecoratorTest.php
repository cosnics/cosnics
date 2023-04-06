<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\PublicationCategoryCourseGroupServiceDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Stub\DefaultPublicationCategoryCourseGroupServiceDecorator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the PublicationCategoryCourseGroupServiceDecorator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationCategoryCourseGroupServiceDecoratorTest extends ChamiloTestCase
{
    /**
     * @var PublicationCategoryCourseGroupServiceDecorator
     */
    protected $publicationCategoryCourseGroupServiceDecorator;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupPublicationCategoryServiceMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->courseGroupPublicationCategoryServiceMock =
            $this->getMockBuilder(CourseGroupPublicationCategoryService::class)
                ->disableOriginalConstructor()->getMock();

        $this->publicationCategoryCourseGroupServiceDecorator =
            new DefaultPublicationCategoryCourseGroupServiceDecorator($this->courseGroupPublicationCategoryServiceMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->courseGroupPublicationCategoryServiceMock);
        unset($this->publicationCategoryCourseGroupServiceDecorator);
    }

    public function testCreateGroup()
    {
        $courseGroup = new CourseGroup();
        $user = new User();
        $formValues = ['use_test' => [1]];

        $this->courseGroupPublicationCategoryServiceMock->expects($this->once())
            ->method('createPublicationCategoryForCourseGroup')
            ->with($courseGroup, 'Test');

        $this->publicationCategoryCourseGroupServiceDecorator->createGroup($courseGroup, $user, $formValues);
    }

    public function testCreateGroupWithoutFormSelection()
    {
        $courseGroup = new CourseGroup();
        $user = new User();
        $formValues = ['use_test' => [0]];

        $this->courseGroupPublicationCategoryServiceMock->expects($this->never())
            ->method('createPublicationCategoryForCourseGroup')
            ->with($courseGroup, 'Test');

        $this->publicationCategoryCourseGroupServiceDecorator->createGroup($courseGroup, $user, $formValues);
    }

    public function testUpdateGroup()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(4);

        $user = new User();
        $formValues = ['use_test' => [4 => 1]];

        $this->courseGroupPublicationCategoryServiceMock->expects($this->once())
            ->method('createOrUpdatePublicationCategoryForCourseGroup')
            ->with($courseGroup, 'Test');

        $this->publicationCategoryCourseGroupServiceDecorator->updateGroup($courseGroup, $user, $formValues);
    }

    public function testUpdateGroupWithoutFormSelection()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(4);

        $user = new User();
        $formValues = ['use_test' => [4 => 0]];

        $this->courseGroupPublicationCategoryServiceMock->expects($this->once())
            ->method('disconnectPublicationCategoryFromCourseGroup')
            ->with($courseGroup, 'Test');

        $this->publicationCategoryCourseGroupServiceDecorator->updateGroup($courseGroup, $user, $formValues);
    }

    public function testDeleteGroup()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(4);

        $user = new User();

        $this->courseGroupPublicationCategoryServiceMock->expects($this->once())
            ->method('disconnectPublicationCategoryFromCourseGroup')
            ->with($courseGroup, 'Test');

        $this->publicationCategoryCourseGroupServiceDecorator->deleteGroup($courseGroup, $user);
    }

    public function testSubscribeUser()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(4);

        $user = new User();

        $this->courseGroupPublicationCategoryServiceMock->expects($this->never())
            ->method($this->anything());

        $this->publicationCategoryCourseGroupServiceDecorator->subscribeUser($courseGroup, $user);
    }

    public function testUnsubscribeUser()
    {
        $courseGroup = new CourseGroup();
        $courseGroup->setId(4);

        $user = new User();

        $this->courseGroupPublicationCategoryServiceMock->expects($this->never())
            ->method($this->anything());

        $this->publicationCategoryCourseGroupServiceDecorator->unsubscribeUser($courseGroup, $user);
    }
}