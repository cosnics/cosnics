<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Unit\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupServiceDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the CourseGroupServiceDecorator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceDecoratorTest extends ChamiloTestCase
{
    /**
     * @var CourseGroupServiceDecorator
     */
    protected $courseGroupServiceDecorator;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupOffice365ConnectorMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->courseGroupOffice365ConnectorMock = $this->getMockBuilder(CourseGroupOffice365Connector::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupServiceDecorator = new CourseGroupServiceDecorator($this->courseGroupOffice365ConnectorMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->courseGroupOffice365ConnectorMock);
        unset($this->courseGroupServiceDecorator);
    }

    public function testCreateGroup()
    {
        $courseGroup = new CourseGroup();
        $user = new User();
        $formValues[CourseGroupFormDecorator::PROPERTY_USE_GROUP] = 1;

        $this->courseGroupOffice365ConnectorMock->expects($this->once())
            ->method('createGroupFromCourseGroup')
            ->with($courseGroup, $user);

        $this->courseGroupServiceDecorator->createGroup($courseGroup, $user, $formValues);
    }

    public function testCreateGroupWithoutFormValues()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->courseGroupOffice365ConnectorMock->expects($this->never())
            ->method('createGroupFromCourseGroup');

        $this->courseGroupServiceDecorator->createGroup($courseGroup, $user);
    }

    public function testUpdateGroup()
    {
        $courseGroup = new CourseGroup();
        $user = new User();
        $formValues[CourseGroupFormDecorator::PROPERTY_USE_GROUP] = 1;

        $this->courseGroupOffice365ConnectorMock->expects($this->once())
            ->method('createOrUpdateGroupFromCourseGroup')
            ->with($courseGroup, $user);

        $this->courseGroupServiceDecorator->updateGroup($courseGroup, $user, $formValues);
    }

    public function testUpdateGroupWithoutFormValues()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->courseGroupOffice365ConnectorMock->expects($this->once())
            ->method('unlinkOffice365GroupFromCourseGroup')
            ->with($courseGroup, $user);

        $this->courseGroupServiceDecorator->updateGroup($courseGroup, $user);
    }

    public function testDeleteGroup()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->courseGroupOffice365ConnectorMock->expects($this->once())
            ->method('unlinkOffice365GroupFromCourseGroup')
            ->with($courseGroup, $user);

        $this->courseGroupServiceDecorator->deleteGroup($courseGroup, $user);
    }

    public function testSubscribeUser()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->courseGroupOffice365ConnectorMock->expects($this->once())
            ->method('subscribeUser')
            ->with($courseGroup, $user);

        $this->courseGroupServiceDecorator->subscribeUser($courseGroup, $user);
    }

    public function testUnsubscribeUser()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->courseGroupOffice365ConnectorMock->expects($this->once())
            ->method('unsubscribeUser')
            ->with($courseGroup, $user);

        $this->courseGroupServiceDecorator->unsubscribeUser($courseGroup, $user);
    }

}

