<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Unit\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365Connector;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;

/**
 * Tests the CourseGroupOffice365Connector
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365ConnectorTest extends ChamiloTestCase
{
    /**
     * @var CourseGroupOffice365Connector
     */
    protected $courseGroupOffice365Connector;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupServiceMock;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userServiceMock;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service\CourseGroupOffice365ReferenceService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseGroupOffice365ReferenceServiceMock;

    /**
     * @var \Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $courseServiceMock;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationConsulterMock;

    /**
     * @var TeamService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $teamService;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->groupServiceMock = $this->getMockBuilder(GroupService::class)
            ->disableOriginalConstructor()->getMock();

        $this->groupRepositoryMock = $this->getMockBuilder(GroupRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->userServiceMock = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()->getMock();

        $this->teamService = $this->getMockBuilder(TeamService::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupOffice365ReferenceServiceMock =
            $this->getMockBuilder(CourseGroupOffice365ReferenceService::class)
                ->disableOriginalConstructor()->getMock();

        $this->courseServiceMock = $this->getMockBuilder(CourseServiceInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->configurationConsulterMock = $this->getMockBuilder(ConfigurationConsulter::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupOffice365Connector = new CourseGroupOffice365Connector(
            $this->groupServiceMock, $this->teamService, $this->userServiceMock,
            $this->courseGroupOffice365ReferenceServiceMock, $this->courseServiceMock, $this->configurationConsulterMock
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->groupServiceMock);
        unset($this->userServiceMock);
        unset($this->configurationConsulterMock);
        unset($this->groupRepositoryMock);
        unset($this->courseGroupOffice365ReferenceServiceMock);
        unset($this->courseServiceMock);
        unset($this->courseGroupOffice365Connector);
    }

    public function testCreateGroupFromCourseGroup()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $courseName = 'How to create a course group in 10 steps';
        $courseCode = 'CURMOBFBO123452017';
        $groupName = 'Test Group 101';

        $office365GroupName = $this->getOffice365GroupName($courseName, $groupName, $courseCode);

        $course = new Course();
        $course->set_visual_code($courseCode);
        $course->set_title($courseName);

        $this->courseServiceMock->expects($this->once())
            ->method('getCourseById')
            ->will($this->returnValue($course));

        $courseGroup->expects($this->once())
            ->method('get_name')
            ->will($this->returnValue($groupName));

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasReference')
            ->with($courseGroup)
            ->will($this->returnValue(false));

        $this->groupServiceMock->expects($this->once())
            ->method('createGroupByName')
            ->with($user, $office365GroupName)
            ->will($this->returnValue(5));

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('createReferenceForCourseGroup')
            ->with($courseGroup, 5);

//        $teacher = new User();
//        $courseTeachers = [$teacher];

//        $this->courseServiceMock->expects($this->once())
//            ->method('getTeachersFromCourse')
//            ->will($this->returnValue($courseTeachers));
//
//        $this->groupServiceMock->expects($this->at(1))
//            ->method('addMemberToGroup')
//            ->with(5, $teacher);

        $courseGroupMember = new User();
        $courseGroupMembers = [$courseGroupMember];

        $courseGroup->expects($this->once())
            ->method('get_members')
            ->with(false, false, true)
            ->will($this->returnValue($courseGroupMembers));

        $this->groupServiceMock->expects($this->at(1))
            ->method('addMemberToGroup')
            ->with(5, $courseGroupMember);

        $this->courseGroupOffice365Connector->createGroupFromCourseGroup($courseGroup, $user);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateGroupFromCourseGroupWithGroupAlreadyCreated()
    {
        $courseGroup = new CourseGroup();
        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $this->courseGroupOffice365Connector->createGroupFromCourseGroup($courseGroup, $user);
    }

//    public function testCreateGroupFromCourseGroupWithExceptionInAddTeacher()
//    {
//        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
//        $courseGroup = $this->getMockBuilder(CourseGroup::class)
//            ->disableOriginalConstructor()->getMock();
//
//        $user = new User();
//
//        $teacher = new User();
//        $courseTeachers = [$teacher];
//
//        $this->courseServiceMock->expects($this->once())
//            ->method('getTeachersFromCourse')
//            ->will($this->returnValue($courseTeachers));
//
//        $this->groupServiceMock->expects($this->at(1))
//            ->method('addMemberToGroup')
//            ->will($this->throwException(new AzureUserNotExistsException($teacher)));
//
//        $this->courseGroupOffice365Connector->createGroupFromCourseGroup($courseGroup, $user);
//    }

    public function testCreateGroupFromCourseGroupWithExceptionInAddMember()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $courseGroupMember = new User();
        $courseGroupMembers = [$courseGroupMember];

        $courseGroup->expects($this->once())
            ->method('get_members')
            ->with(false, false, true)
            ->will($this->returnValue($courseGroupMembers));

        $this->groupServiceMock
            ->expects($this->once())
            ->method('createGroupByName')
            ->willReturn("a");
        $this->groupServiceMock->expects($this->at(1))
            ->method('addMemberToGroup')
            ->with("a", $courseGroupMember)
            ->will($this->throwException(new AzureUserNotExistsException($courseGroupMember)));

        $this->courseGroupOffice365Connector->createGroupFromCourseGroup($courseGroup, $user);
    }

    public function testCreateOrUpdateGroupFromCourseGroup()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $groupName = 'Test Group 101';

        $courseGroup->expects($this->once())
            ->method('get_name')
            ->will($this->returnValue($groupName));

        $user = new User();

        $this->groupServiceMock->expects($this->once())
            ->method('createGroupByName')
            ->with($user, $groupName)
            ->will($this->returnValue(5));

        $this->courseGroupOffice365Connector->createOrUpdateGroupFromCourseGroup($courseGroup, $user);
    }

    public function testCreateOrUpdateGroupFromCourseGroupWithLinkedReference()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $groupName = 'Test Group 101';

        $courseGroup->expects($this->once())
            ->method('get_name')
            ->will($this->returnValue($groupName));

        $courseName = 'How to create a course group in 10 steps';
        $courseCode = 'CURMOBFBO123452017';
        $office365GroupName = $this->getOffice365GroupName($courseName, $groupName, $courseCode);

        $course = new Course();
        $course->set_visual_code($courseCode);
        $course->set_title($courseName);

        $this->courseServiceMock->expects($this->once())
            ->method('getCourseById')
            ->will($this->returnValue($course));

        $user = new User();

        $reference = new CourseGroupOffice365Reference();
        $reference->setLinked(true);
        $reference->setOffice365GroupId(5);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->groupServiceMock->expects($this->once())
            ->method('updateGroupName')
            ->with('5', $office365GroupName);

        $this->courseGroupOffice365Connector->createOrUpdateGroupFromCourseGroup($courseGroup, $user);
    }

    public function testCreateOrUpdateGroupFromCourseGroupWithUnlinkedReference()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $groupName = 'Test Group 101';

        $courseGroup->expects($this->once())
            ->method('get_name')
            ->will($this->returnValue($groupName));

        $user = new User();

        $reference = new CourseGroupOffice365Reference();
        $reference->setLinked(false);
        $reference->setOffice365GroupId(5);

        $courseName = 'How to create a course group in 10 steps';
        $courseCode = 'CURMOBFBO123452017';
        $office365GroupName = $this->getOffice365GroupName($courseName, $groupName, $courseCode);

        $course = new Course();
        $course->set_visual_code($courseCode);
        $course->set_title($courseName);

        $this->courseServiceMock->expects($this->once())
            ->method('getCourseById')
            ->will($this->returnValue($course));

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('linkCourseGroupReference')
            ->with($reference);

        $this->groupServiceMock->expects($this->once())
            ->method('updateGroupName')
            ->with('5', $office365GroupName);

        $this->groupServiceMock->expects($this->at(1))
            ->method('addMemberToGroup')
            ->with(5, $user);

//        $teacher = new User();
//        $courseTeachers = [$teacher];
//
//        $this->courseServiceMock->expects($this->once())
//            ->method('getTeachersFromCourse')
//            ->will($this->returnValue($courseTeachers));
//
//        $this->groupServiceMock->expects($this->at(2))
//            ->method('addMemberToGroup')
//            ->with(5, $teacher);

        $courseGroupMember = new User();
        $courseGroupMembers = [$courseGroupMember];

        $courseGroup->expects($this->once())
            ->method('get_members')
            ->with(false, false, true)
            ->will($this->returnValue($courseGroupMembers));

        $this->groupServiceMock->expects($this->at(2))
            ->method('addMemberToGroup')
            ->with(5, $courseGroupMember);

        $this->courseGroupOffice365Connector->createOrUpdateGroupFromCourseGroup($courseGroup, $user);
    }

    public function testUnlinkOffice365GroupFromCourseGroup()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->groupServiceMock->expects($this->once())
            ->method('removeAllMembersFromGroup')
            ->with(5);

        $this->groupServiceMock->expects($this->once())
            ->method('addMemberToGroup')
            ->with(5, $user);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('unlinkCourseGroupReference')
            ->with($reference);

        $this->courseGroupOffice365Connector->unlinkOffice365GroupFromCourseGroup($courseGroup, $user);
    }

    public function testUnlinkOffice365GroupFromCourseGroupWithUnlinkedCourseGroup()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(false));


        $this->groupServiceMock->expects($this->never())
            ->method('removeAllMembersFromGroup');

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->never())
            ->method('unlinkCourseGroupReference');

        $this->courseGroupOffice365Connector->unlinkOffice365GroupFromCourseGroup($courseGroup, $user);
    }

    public function testUnlinkOffice365GroupFromCourseGroupWithInvalidUser()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->groupServiceMock->expects($this->once())
            ->method('addMemberToGroup')
            ->will($this->throwException(new AzureUserNotExistsException($user)));

        $this->courseGroupOffice365Connector->unlinkOffice365GroupFromCourseGroup($courseGroup, $user);
    }

    public function testSubscribeUser()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->groupServiceMock->expects($this->once())
            ->method('addMemberToGroup')
            ->with(5, $user);

        $this->courseGroupOffice365Connector->subscribeUser($courseGroup, $user);
    }

    public function testSubscribeUserWithUnlinkedCourseGroup()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(false));

        $this->groupServiceMock->expects($this->never())
            ->method('addMemberToGroup');

        $this->courseGroupOffice365Connector->subscribeUser($courseGroup, $user);
    }

    public function testSubscribeUserWithInvalidUser()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->groupServiceMock->expects($this->once())
            ->method('addMemberToGroup')
            ->will($this->throwException(new AzureUserNotExistsException($user)));

        $this->courseGroupOffice365Connector->subscribeUser($courseGroup, $user);
    }

    public function testUnsubscribeUser()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->groupServiceMock->expects($this->once())
            ->method('removeMemberFromGroup')
            ->with(5, $user);

        $this->courseGroupOffice365Connector->unsubscribeUser($courseGroup, $user);
    }

    public function testUnsubscribeUserWithUnlinkedCourseGroup()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(false));

        $this->groupServiceMock->expects($this->never())
            ->method('removeMemberFromGroup');

        $this->courseGroupOffice365Connector->unsubscribeUser($courseGroup, $user);
    }

    public function testUnsubscribeUserWithInvalidUser()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $user = new User();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('courseGroupHasLinkedReference')
            ->with($courseGroup)
            ->will($this->returnValue(true));

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $this->groupServiceMock->expects($this->once())
            ->method('removeMemberFromGroup')
            ->will($this->throwException(new AzureUserNotExistsException($user)));

        $this->courseGroupOffice365Connector->unsubscribeUser($courseGroup, $user);
    }

    public function testSyncCourseGroupSubscriptions()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);
        $reference->setLinked(true);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

//        $teacher1 = new User();
//        $teacher1->setId(1);
//        $courseTeachers = [$teacher1];
//
//        $this->courseServiceMock->expects($this->once())
//            ->method('getTeachersFromCourse')
//            ->will($this->returnValue($courseTeachers));

        $courseGroupMember = new User();
        $courseGroupMember->setId(3);

        $courseGroupMember2 = new User();
        $courseGroupMember2->setId(1);

        $courseGroupMembers = [$courseGroupMember, $courseGroupMember2];

        $courseGroup->expects($this->once())
            ->method('get_members')
            ->with(true, true, true)
            ->will($this->returnValue($courseGroupMembers));

        $this->courseGroupOffice365Connector->syncCourseGroupSubscriptions($courseGroup);
    }

    /**
     * Test to make sure that teachers are not removed when they are subscribed to the group when using the sync button
     */
    public function testSyncCourseGroupSubscriptionsWithSubscribedTeachers()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $reference = new CourseGroupOffice365Reference();
        $reference->setOffice365GroupId(5);
        $reference->setLinked(true);

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue($reference));

        $teacher1 = new User();
        $teacher1->setId(6);
        $courseTeachers = [$teacher1];

        $courseGroupMember = new User();
        $courseGroupMember->setId(3);

        $courseGroupMembers = [$courseGroupMember];

        $courseGroup->expects($this->once())
            ->method('get_members')
            ->with(true, true, true)
            ->will($this->returnValue($courseGroupMembers));

        $this->courseGroupOffice365Connector->syncCourseGroupSubscriptions($courseGroup);
    }

    public function testSyncCourseGroupSubscriptionsWithUnlinkedCourseGroup()
    {
        /** @var CourseGroup | \PHPUnit_Framework_MockObject_MockObject $courseGroup */
        $courseGroup = $this->getMockBuilder(CourseGroup::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
            ->method('getCourseGroupReference')
            ->with($courseGroup)
            ->will($this->returnValue(null));

        $this->groupRepositoryMock->expects($this->never())
            ->method('subscribeMemberInGroup');

        $this->groupRepositoryMock->expects($this->never())
            ->method('removeMemberFromGroup');

        $this->courseGroupOffice365Connector->syncCourseGroupSubscriptions($courseGroup);
    }

//    public function testGetPlannerUrlForVisit()
//    {
//        $courseGroup = new CourseGroup();
//        $user = new User();
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('courseGroupHasLinkedReference')
//            ->with($courseGroup)
//            ->will($this->returnValue(true));
//
//        $reference = new CourseGroupOffice365Reference();
//        $reference->setOffice365GroupId(5);
//        $reference->setOffice365PlanId(10);
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('getCourseGroupReference')
//            ->with($courseGroup)
//            ->will($this->returnValue($reference));
//
//        $this->groupServiceMock->expects($this->once())
//            ->method('addMemberToGroup')
//            ->with(5, $user);
//
//        $baseUrl = 'https://tasks.office.com/example.onmicrosoft.com/nl-NL/Home/Planner/#/';
//
//        $this->configurationConsulterMock->expects($this->once())
//            ->method('getSetting')
//            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'planner_base_uri'])
//            ->will($this->returnValue($baseUrl));
//
//        $url = 'https://tasks.office.com/example.onmicrosoft.com/nl-NL/Home/Planner/#//#/plantaskboard?groupId=5&planId=10';
//
//        $this->assertEquals($url, $this->courseGroupOffice365Connector->getPlannerUrlForVisit($courseGroup, $user));
//    }
//
//    /**
//     * @expectedException \RuntimeException
//     */
//    public function testGetPlannerUrlForVisitWithoutValidReference()
//    {
//        $courseGroup = new CourseGroup();
//        $user = new User();
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('courseGroupHasLinkedReference')
//            ->with($courseGroup)
//            ->will($this->returnValue(false));
//
//        $this->courseGroupOffice365Connector->getPlannerUrlForVisit($courseGroup, $user);
//    }
//
//    public function testGetPlannerUrlForVisitNoPlanId()
//    {
//        $courseGroup = new CourseGroup();
//        $user = new User();
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('courseGroupHasLinkedReference')
//            ->with($courseGroup)
//            ->will($this->returnValue(true));
//
//        $reference = new CourseGroupOffice365Reference();
//        $reference->setOffice365GroupId(5);
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('getCourseGroupReference')
//            ->with($courseGroup)
//            ->will($this->returnValue($reference));
//
//        $this->groupServiceMock->expects($this->once())
//            ->method('addMemberToGroup')
//            ->with(5, $user);
//
//        $baseUrl = 'https://tasks.office.com/example.onmicrosoft.com/nl-NL/Home/Planner/#/';
//
//        $this->configurationConsulterMock->expects($this->once())
//            ->method('getSetting')
//            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'planner_base_uri'])
//            ->will($this->returnValue($baseUrl));
//
//        $this->groupServiceMock->expects($this->once())
//            ->method('getOrCreatePlanIdForGroup')
//            ->with(5)
//            ->will($this->returnValue(10));
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('storePlannerReferenceForCourseGroup')
//            ->with($courseGroup, 5, 10);
//
//        $url = 'https://tasks.office.com/example.onmicrosoft.com/nl-NL/Home/Planner/#//#/plantaskboard?groupId=5&planId=10';
//
//        $this->assertEquals($url, $this->courseGroupOffice365Connector->getPlannerUrlForVisit($courseGroup, $user));
//    }

//    public function testGetGroupUrlForVisit()
//    {
//        $courseGroup = new CourseGroup();
//        $user = new User();
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('courseGroupHasLinkedReference')
//            ->with($courseGroup)
//            ->will($this->returnValue(true));
//
//        $reference = new CourseGroupOffice365Reference();
//        $reference->setOffice365GroupId(5);
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('getCourseGroupReference')
//            ->with($courseGroup)
//            ->will($this->returnValue($reference));
//
//        $this->groupServiceMock->expects($this->once())
//            ->method('addMemberToGroup')
//            ->with(5, $user);
//
//        $baseUrl = 'https://outlook.office.com/owa/?realm=example.com&exsvurl=1&ll-cc=1043&modurl=0&path=/group/{GROUP_ID}@example.onmicrosoft.com/mail';
//
//        $this->configurationConsulterMock->expects($this->once())
//            ->method('getSetting')
//            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'group_base_uri'])
//            ->will($this->returnValue($baseUrl));
//
//        $group = new \Microsoft\Graph\Model\Group(['mailNickname' => 'MyFirstGroup']);
//
//        $this->groupServiceMock->expects($this->once())
//            ->method('getGroup')
//            ->with(5)
//            ->will($this->returnValue($group));
//
//        $url = 'https://outlook.office.com/owa/?realm=example.com&exsvurl=1&ll-cc=1043&modurl=0&path=/group/MyFirstGroup@example.onmicrosoft.com/mail';
//
//        $this->assertEquals($url, $this->courseGroupOffice365Connector->getGroupUrlForVisit($courseGroup, $user));
//    }
//
//    /**
//     * @expectedException \RuntimeException
//     */
//    public function testGetGroupUrlForVisitValidReference()
//    {
//        $courseGroup = new CourseGroup();
//        $user = new User();
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('courseGroupHasLinkedReference')
//            ->with($courseGroup)
//            ->will($this->returnValue(false));
//
//        $this->courseGroupOffice365Connector->getGroupUrlForVisit($courseGroup, $user);
//    }
//
//    /**
//     * @expectedException \RuntimeException
//     */
//    public function testGetGroupUrlForVisitWithoutValidGroup()
//    {
//        $courseGroup = new CourseGroup();
//        $user = new User();
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('courseGroupHasLinkedReference')
//            ->with($courseGroup)
//            ->will($this->returnValue(true));
//
//        $reference = new CourseGroupOffice365Reference();
//        $reference->setOffice365GroupId(5);
//
//        $this->courseGroupOffice365ReferenceServiceMock->expects($this->once())
//            ->method('getCourseGroupReference')
//            ->with($courseGroup)
//            ->will($this->returnValue($reference));
//
//        $this->groupServiceMock->expects($this->once())
//            ->method('addMemberToGroup')
//            ->with(5, $user);
//
//        $baseUrl = 'https://outlook.office.com/owa/?realm=example.com&exsvurl=1&ll-cc=1043&modurl=0&path=/group/{GROUP_ID}@example.onmicrosoft.com/mail';
//
//        $this->configurationConsulterMock->expects($this->once())
//            ->method('getSetting')
//            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'group_base_uri'])
//            ->will($this->returnValue($baseUrl));
//
//        $this->groupServiceMock->expects($this->once())
//            ->method('getGroup')
//            ->with(5)
//            ->willThrowException(new GroupNotExistsException('5'));
//
//        $this->courseGroupOffice365Connector->getGroupUrlForVisit($courseGroup, $user);
//    }

    /**
     * @param string $courseName
     * @param string $groupName
     * @param string $courseCode
     * @return string
     */
    protected function getOffice365GroupName(string $courseName, string $groupName, string $courseCode): string
    {
        return  $groupName . ' - ' . $courseName . ' (' . $courseCode . ')';
    }

}

