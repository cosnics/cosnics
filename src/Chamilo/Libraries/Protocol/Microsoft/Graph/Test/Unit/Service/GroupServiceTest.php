<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use Microsoft\Graph\Model\Group;
use Microsoft\Graph\Model\PlannerPlan;

/**
 * Tests the GroupService
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupServiceTest extends ChamiloTestCase
{
    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupRepositoryMock;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userServiceMock;

    /**
     * @var ConfigurationConsulter | \PHPUnit\Framework\MockObject\MockObject
     */
    protected $configurationConsulterMock;

    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->groupRepositoryMock = $this->getMockBuilder(GroupRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->userServiceMock = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()->getMock();

        $this->configurationConsulterMock = $this->getMockBuilder(ConfigurationConsulter::class)
            ->disableOriginalConstructor()->getMock();

        $this->groupService =
            new GroupService($this->userServiceMock, $this->groupRepositoryMock, $this->configurationConsulterMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->userServiceMock);
        unset($this->groupRepositoryMock);
        unset($this->groupService);
    }

    public function testCreateGroupByName()
    {
        $groupName = 'TestGroup 101';
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';

        $this->mockGetAzureUser($user, $externalUserIdentifier);

        $this->groupRepositoryMock->expects($this->once())
            ->method('createGroup')
            ->with($groupName)
            ->will($this->returnValue(new Group(['id' => 5])));

        $this->groupRepositoryMock->expects($this->once())
            ->method('subscribeMemberInGroup')
            ->with(5, $externalUserIdentifier);

        $this->assertEquals(5, $this->groupService->createGroupByName($user, $groupName));
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function testCreateGroupByNameWithInvalidUser()
    {
        $groupName = 'TestGroup 101';
        $user = new User();
        $this->mockGetAzureUser($user, null);

        $this->groupService->createGroupByName($user, $groupName);
    }

    public function testUpdateGroupName()
    {
        $groupName = 'TestGroup 101';
        $groupId = 5;

        $this->groupRepositoryMock->expects($this->once())
            ->method('updateGroup')
            ->with($groupId, $groupName);

        $this->groupService->updateGroupName($groupId, $groupName);
    }

    public function testAddMemberToGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier);

        $this->groupRepositoryMock->expects($this->once())
            ->method('subscribeMemberInGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->groupService->addMemberToGroup($groupId, $user);
    }

    public function testAddMemberToGroupWhenAlreadyInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());
        $this->groupRepositoryMock->expects($this->never())
            ->method('subscribeMemberInGroup');

        $this->groupService->addMemberToGroup($groupId, $user);
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function testAddMemberToGroupWithInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetAzureUser($user, null);
        $this->groupService->addMemberToGroup($groupId, $user);
    }

    public function testRemoveMemberFromGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->groupRepositoryMock->expects($this->once())
            ->method('removeMemberFromGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->groupService->removeMemberFromGroup($groupId, $user);
    }

    public function testRemoveMemberFromGroupNotInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier);

        $this->groupRepositoryMock->expects($this->never())
            ->method('removeMemberFromGroup');

        $this->groupService->removeMemberFromGroup($groupId, $user);
    }

    public function testRemoveMemberFromGroupInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetAzureUser($user);

        $this->groupRepositoryMock->expects($this->never())
            ->method('removeMemberFromGroup');

        $this->groupService->removeMemberFromGroup($groupId, $user);
    }

    public function testIsMemberOfGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->assertTrue($this->groupService->isMemberOfGroup($groupId, $user));
    }

    public function testIsMemberOfGroupNotSubscribed()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier);

        $this->assertFalse($this->groupService->isMemberOfGroup($groupId, $user));
    }

    public function testIsMemberOfGroupInvalidUser()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier);

        $this->assertFalse($this->groupService->isMemberOfGroup($groupId, $user));
    }

    public function testGetGroupMembers()
    {
        $groupMembers = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
            new \Microsoft\Graph\Model\User(['id' => 6])
        ];

        $groupId = 5;

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupMembers')
            ->with($groupId)
            ->will($this->returnValue($groupMembers));

        $this->assertEquals([4, 6], $this->groupService->getGroupMembers($groupId));
    }

    public function testRemoveAllMembersFromGroup()
    {
        $groupMembers = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
        ];

        $groupId = 5;

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupMembers')
            ->with($groupId)
            ->will($this->returnValue($groupMembers));

        $this->groupRepositoryMock->expects($this->once())
            ->method('removeMemberFromGroup')
            ->with($groupId, 4);

        $this->groupService->removeAllMembersFromGroup($groupId);
    }

    public function testAddOwnerToGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier);

        $this->groupRepositoryMock->expects($this->once())
            ->method('subscribeOwnerInGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->groupService->addOwnerToGroup($groupId, $user);
    }

    public function testAddOwnerToGroupWhenAlreadyInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());
        $this->groupRepositoryMock->expects($this->never())
            ->method('subscribeMemberInGroup');

        $this->groupService->addOwnerToGroup($groupId, $user);
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function testAddOwnerToGroupWithInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetAzureUser($user, null);
        $this->groupService->addOwnerToGroup($groupId, $user);
    }

    public function testRemoveOwnerFromGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->groupRepositoryMock->expects($this->once())
            ->method('removeOwnerFromGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->groupService->removeOwnerFromGroup($groupId, $user);
    }

    public function testRemoveOwnerFromGroupNotInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier);

        $this->groupRepositoryMock->expects($this->never())
            ->method('removeOwnerFromGroup');

        $this->groupService->removeOwnerFromGroup($groupId, $user);
    }

    public function testRemoveOwnerFromGroupInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetAzureUser($user);

        $this->groupRepositoryMock->expects($this->never())
            ->method('removeOwnerFromGroup');

        $this->groupService->removeOwnerFromGroup($groupId, $user);
    }

    public function testIsOwnerOfGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->assertTrue($this->groupService->isOwnerOfGroup($groupId, $user));
    }

    public function testIsOwnerOfGroupNotSubscribed()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier);

        $this->assertFalse($this->groupService->isOwnerOfGroup($groupId, $user));
    }

    public function testIsOwnerOfGroupInvalidUser()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetAzureUser($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier);

        $this->assertFalse($this->groupService->isOwnerOfGroup($groupId, $user));
    }

    public function testGetGroupOwners()
    {
        $groupOwners = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
            new \Microsoft\Graph\Model\User(['id' => 6])
        ];

        $groupId = 5;

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupOwners')
            ->with($groupId)
            ->will($this->returnValue($groupOwners));

        $this->assertEquals([4, 6], $this->groupService->getGroupOwners($groupId));
    }

    public function testRemoveAllOwnersFromGroup()
    {
        $groupOwners = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
        ];

        $groupId = 5;

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupOwners')
            ->with($groupId)
            ->will($this->returnValue($groupOwners));

        $this->groupRepositoryMock->expects($this->once())
            ->method('removeOwnerFromGroup')
            ->with($groupId, 4);

        $this->groupService->removeAllOwnersFromGroup($groupId);
    }

    public function testGetGroupPlanIds()
    {
        $groupId = 5;

        $groupPlans = [
            new PlannerPlan(['id' => 3]),
            new PlannerPlan(['id' => 9]),
        ];

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue($groupPlans));

        $this->assertEquals([3, 9], $this->groupService->getGroupPlanIds($groupId));
    }

    public function testGetDefaultGroupPlanId()
    {
        $groupId = 5;

        $groupPlans = [
            new PlannerPlan(['id' => 3]),
            new PlannerPlan(['id' => 9]),
        ];

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue($groupPlans));

        $this->assertEquals(3, $this->groupService->getDefaultGroupPlanId($groupId));
    }

    public function testGetDefaultGroupPlanIdNoPlans()
    {
        $groupId = 5;
        $groupPlans = [];

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue($groupPlans));

        $this->assertEmpty($this->groupService->getDefaultGroupPlanId($groupId));
    }

    public function testCreatePlanForGroup()
    {
        $groupId = 5;
        $planName = 'Planning for Groups 101';

        $this->groupRepositoryMock->expects($this->once())
            ->method('createPlanForGroup')
            ->with($groupId, $planName)
            ->will($this->returnValue(new PlannerPlan(['id' => 3])));

        $this->assertEquals(3, $this->groupService->createPlanForGroup($groupId, $planName));
    }

    public function testCreatePlanForGroupNoPlanName()
    {
        $groupId = 5;
        $planName = 'Planning for Groups 101';

        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroup')
            ->with($groupId)
            ->will($this->returnValue(new Group(['id' => 5, 'displayName' => $planName])));

        $this->groupRepositoryMock->expects($this->once())
            ->method('createPlanForGroup')
            ->with($groupId, $planName)
            ->will($this->returnValue(new PlannerPlan(['id' => 3])));

        $this->assertEquals(3, $this->groupService->createPlanForGroup($groupId));
    }

    public function testGetOrCreatePlanIdForGroup()
    {
        $groupId = 5;

        $groupPlans = [
            new PlannerPlan(['id' => 3]),
            new PlannerPlan(['id' => 9]),
        ];

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue($groupPlans));

        $this->assertEquals(3, $this->groupService->getOrCreatePlanIdForGroup($groupId));
    }

    public function testGetOrCreatePlanIdForGroupNoPlanFound()
    {
        $groupId = 5;
        $planName = 'Planning for Groups 101';

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue([]));

        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroup')
            ->with($groupId)
            ->will($this->returnValue(new Group(['id' => 5, 'displayName' => $planName])));

        $this->groupRepositoryMock->expects($this->once())
            ->method('createPlanForGroup')
            ->with($groupId, $planName)
            ->will($this->returnValue(new PlannerPlan(['id' => 3])));

        $this->assertEquals(3, $this->groupService->getOrCreatePlanIdForGroup($groupId));
    }

    public function testSyncUsersToGroup()
    {
        $groupId = 5;

        $user1 = new User();
        $user1->setId(8);

        $user2 = new User();
        $user2->setId(20);

        $users = [];
        $users[] = $user1;
        $users[] = $user2;

        $excludedUser = new User();
        $excludedUser->setId(10);

        $exlucdedUsers = [$excludedUser];

        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroup')
            ->with($groupId)
            ->will($this->returnValue(new Group(['id' => 5, 'displayName' => 'Group 101'])));

        $this->userServiceMock->expects($this->exactly(3))
            ->method('getAzureUserIdentifier')
            ->will(
                $this->returnCallback(
                    function (User $user) {
                        return $user->getId();
                    }
                )
            );

        $this->groupRepositoryMock->expects($this->once())
            ->method('listGroupMembers')
            ->will(
                $this->returnValue(
                    [new \Microsoft\Graph\Model\User(['id' => 6]), new \Microsoft\Graph\Model\User(['id' => 10])]
                )
            );

        $this->groupRepositoryMock->expects($this->exactly(2))
            ->method('subscribeMemberInGroup')
            ->withConsecutive(
                [5, 8], [5, 20]
            );

        $this->groupRepositoryMock->expects($this->once())
            ->method('removeMemberFromGroup')
            ->with(5, 6);

        $this->groupService->syncUsersToGroup($groupId, $users, $exlucdedUsers);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSyncUsersToGroupWithInvalidGroup()
    {
        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroup')
            ->with(5)
            ->will($this->returnValue(null));

        $this->groupService->syncUsersToGroup(5, []);
    }

    public function testGetGroupUrl()
    {
        $groupId = 5;

        $groupUrl = 'http://GOTO.GROUP/{GROUP_ID}';

        $this->configurationConsulterMock->expects($this->once())
            ->method('getSetting')
            ->with(['Chamilo\Libraries\Protocol\Microsoft\Graph', 'group_base_uri'])
            ->will($this->returnValue($groupUrl));

        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroup')
            ->with($groupId)
            ->will(
                $this->returnValue(
                    new Group(
                        ['id' => 5, 'displayName' => 'Group 101', 'mailNickname' => 'testgroup@microsoft.com']
                    )
                )
            );

        $this->assertEquals('http://GOTO.GROUP/testgroup@microsoft.com', $this->groupService->getGroupUrl($groupId));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetGroupUrlWithInvalidGroup()
    {
        $groupId = 5;

        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroup')
            ->with($groupId)
            ->will($this->returnValue(null));

        $this->groupService->getGroupUrl($groupId);
    }

    /**
     * Mocks the getGroupMember function of the GroupRepository
     *
     * @param string $groupId
     * @param string $externalUserIdentifier
     * @param \Microsoft\Graph\Model\User $returnValue
     */
    protected function mockGetGroupMember($groupId, $externalUserIdentifier, $returnValue = null)
    {
        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroupMember')
            ->with($groupId, $externalUserIdentifier)
            ->will($this->returnValue($returnValue));
    }

    /**
     * Mocks the getGroupMember function of the GroupRepository
     *
     * @param string $groupId
     * @param string $externalUserIdentifier
     * @param \Microsoft\Graph\Model\User $returnValue
     */
    protected function mockGetGroupOwner($groupId, $externalUserIdentifier, $returnValue = null)
    {
        $this->groupRepositoryMock->expects($this->once())
            ->method('getGroupOwner')
            ->with($groupId, $externalUserIdentifier)
            ->will($this->returnValue($returnValue));
    }

    protected function mockGetAzureUser(User $user, $azureUserIdentifier = 5)
    {
        $this->userServiceMock->expects($this->atLeastOnce())
            ->method('getAzureUserIdentifier')
            ->with($user)
            ->will($this->returnValue($azureUserIdentifier));
    }
}

