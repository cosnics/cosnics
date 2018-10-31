<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Unit\Service;

use \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
use \Symfony\Component\Translation\Translator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupActionsDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;

/**
 * Tests the CourseGroupActionsDecorator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupActionsDecoratorTest extends ChamiloTestCase
{
    /**
     * @var CourseGroupActionsDecorator
     */
    protected $courseGroupActionsDecorator;

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlGeneratorMock;

    /**
     * @var \Symfony\Component\Translation\Translator | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupOffice365ReferenceServiceMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->urlGeneratorMock = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()->getMock();

        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupOffice365ReferenceServiceMock =
            $this->getMockBuilder(CourseGroupOffice365ReferenceService::class)
                ->disableOriginalConstructor()->getMock();

        $this->courseGroupActionsDecorator = new CourseGroupActionsDecorator(
            $this->urlGeneratorMock, $this->translatorMock, $this->courseGroupOffice365ReferenceServiceMock
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->urlGeneratorMock);
        unset($this->translatorMock);
        unset($this->courseGroupOffice365ReferenceServiceMock);
        unset($this->courseGroupActionsDecorator);
    }

    public function testAddCourseGroupActions()
    {
        $courseGroup = new CourseGroup();
        $buttonToolbar = new ButtonToolBar();
        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $this->courseGroupActionsDecorator->addCourseGroupActions($buttonToolbar, $courseGroup, $user, true);

        /** @var SplitDropdownButton $splitDropDownButton */
        $splitDropDownButton = $buttonToolbar->getItems()[0];
        $this->assertInstanceOf(SplitDropdownButton::class, $splitDropDownButton);

        $this->assertCount(2, $splitDropDownButton->getSubButtons());
    }

    public function testAddCourseGroupActionsNoTeacher()
    {
        $courseGroup = new CourseGroup();
        $buttonToolbar = new ButtonToolBar();
        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $this->courseGroupActionsDecorator->addCourseGroupActions($buttonToolbar, $courseGroup, $user, false);

        /** @var SplitDropdownButton $splitDropDownButton */
        $splitDropDownButton = $buttonToolbar->getItems()[0];
        $this->assertInstanceOf(SplitDropdownButton::class, $splitDropDownButton);

        $this->assertCount(1, $splitDropDownButton->getSubButtons());
    }

    public function testAddCourseGroupActionsNoReference()
    {
        $courseGroup = new CourseGroup();
        $buttonToolbar = new ButtonToolBar();
        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(false));

        $this->courseGroupActionsDecorator->addCourseGroupActions($buttonToolbar, $courseGroup, $user, false);

        $this->assertFalse($buttonToolbar->hasItems());
    }
}

