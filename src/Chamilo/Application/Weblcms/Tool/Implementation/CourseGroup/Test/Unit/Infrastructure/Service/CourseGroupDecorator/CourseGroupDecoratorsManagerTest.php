<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupActionsDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;

/**
 * Tests the CourseGroupDecoratorsManager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupDecoratorsManagerTest extends ChamiloTestCase
{
    /**
     * @var CourseGroupDecoratorsManager
     */
    protected $courseGroupDecoratorsManager;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formDecoratorMock;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupActionsDecoratorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionsDecoratorMock;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serviceDecoratorMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->courseGroupDecoratorsManager = new CourseGroupDecoratorsManager();

        $this->formDecoratorMock = $this->getMockBuilder(CourseGroupFormDecoratorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->actionsDecoratorMock = $this->getMockBuilder(CourseGroupActionsDecoratorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->serviceDecoratorMock = $this->getMockBuilder(CourseGroupServiceDecoratorInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->courseGroupDecoratorsManager);
    }

    public function testAddFormDecorator()
    {
        $this->addFormDecorator();
        $this->assertEquals([$this->formDecoratorMock], $this->courseGroupDecoratorsManager->getFormDecorators());
    }

    public function testAddActionsDecorator()
    {
        $this->addActionsDecorator();
        $this->assertEquals([$this->actionsDecoratorMock], $this->courseGroupDecoratorsManager->getActionsDecorators());
    }

    public function testAddServiceDecorators()
    {
        $this->addServiceDecorator();
        $this->assertEquals([$this->serviceDecoratorMock], $this->courseGroupDecoratorsManager->getServiceDecorators());
    }

    public function testDecorateCourseGroupForm()
    {
        $formValidator = new FormValidator();
        $courseGroup = new CourseGroup();

        $this->addFormDecorator();

        $this->formDecoratorMock->expects($this->once())
            ->method('decorateCourseGroupForm')
            ->with($formValidator, $courseGroup);

        $this->courseGroupDecoratorsManager->decorateCourseGroupForm($formValidator, $courseGroup);
    }

    public function testCreateGroup()
    {
        $courseGroup = new CourseGroup();
        $user = new User();
        $formValues = ['property1' => 'value1'];

        $this->addServiceDecorator();

        $this->serviceDecoratorMock->expects($this->once())
            ->method('createGroup')
            ->with($courseGroup, $user, $formValues);

        $this->courseGroupDecoratorsManager->createGroup($courseGroup, $user, $formValues);
    }

    public function testUpdateGroup()
    {
        $courseGroup = new CourseGroup();
        $user = new User();
        $formValues = ['property1' => 'value1'];

        $this->addServiceDecorator();

        $this->serviceDecoratorMock->expects($this->once())
            ->method('updateGroup')
            ->with($courseGroup, $user, $formValues);

        $this->courseGroupDecoratorsManager->updateGroup($courseGroup, $user, $formValues);
    }

    public function testDeleteGroup()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->addServiceDecorator();

        $this->serviceDecoratorMock->expects($this->once())
            ->method('deleteGroup')
            ->with($courseGroup, $user);

        $this->courseGroupDecoratorsManager->deleteGroup($courseGroup, $user);
    }

    public function testSubscribeUser()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->addServiceDecorator();

        $this->serviceDecoratorMock->expects($this->once())
            ->method('subscribeUser')
            ->with($courseGroup, $user);

        $this->courseGroupDecoratorsManager->subscribeUser($courseGroup, $user);
    }

    public function testUnsubscribeUser()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->addServiceDecorator();

        $this->serviceDecoratorMock->expects($this->once())
            ->method('unsubscribeUser')
            ->with($courseGroup, $user);

        $this->courseGroupDecoratorsManager->unsubscribeUser($courseGroup, $user);
    }

    public function testAddCourseGroupActions()
    {
        $buttonToolbar = new ButtonToolBar();
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->addActionsDecorator();

        $this->actionsDecoratorMock->expects($this->once())
            ->method('addCourseGroupActions')
            ->with($buttonToolbar, $courseGroup, $user, true);

        $this->courseGroupDecoratorsManager->addCourseGroupActions($buttonToolbar, $courseGroup, $user, true);
    }

    protected function addFormDecorator()
    {
        $this->courseGroupDecoratorsManager->addFormDecorator($this->formDecoratorMock);
    }

    protected function addActionsDecorator()
    {
        $this->courseGroupDecoratorsManager->addActionsDecorator($this->actionsDecoratorMock);
    }

    protected function addServiceDecorator()
    {
        $this->courseGroupDecoratorsManager->addServiceDecorator($this->serviceDecoratorMock);
    }
}

