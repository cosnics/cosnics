<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Unit\Storage\Repository;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GroupRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub;

/**
 * Tests the GroupRepository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupRepositoryTest extends ChamiloTestCase
{
    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    private $graphRepositoryMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void
    {
        $this->graphRepositoryMock = $this->getMockBuilder(GraphRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->groupRepository = new GroupRepository($this->graphRepositoryMock);
    }

    /**
     * Tear down after each test
     */
    protected  function tearDown(): void
    {
        unset($this->graphRepositoryMock);
        unset($this->groupRepository);
    }

    public function testCreateGroup()
    {
        $groupName = 'TestGroup 101';

        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName,
            'mailEnabled' => false,
            'groupTypes' => [
                'Unified',
            ],
            'securityEnabled' => false,
            'mailNickname' => 'TestGroup_101',
            'visibility' => 'private'
        ];

        $group = new \Microsoft\Graph\Model\Group();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executePostWithAccessTokenExpirationRetry')
            ->with(
                '/groups', $this->callback(
                function ($groupData) use ($groupName) {
                    return $groupData['displayName'] = $groupName
                        && $groupData['displayName'];
                }
            ), \Microsoft\Graph\Model\Group::class
            )
            ->will($this->returnValue($group));

        $this->assertEquals($group, $this->groupRepository->createGroup($groupName));
    }

    public function testUpdateGroup()
    {
        $groupIdentifier = 5;
        $groupName = 'TestGroup 101';

        $groupData = [
            'description' => $groupName,
            'displayName' => $groupName,
        ];

        $group = new \Microsoft\Graph\Model\Group();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executePatchWithAccessTokenExpirationRetry')
            ->with('/groups/' . $groupIdentifier, $groupData, \Microsoft\Graph\Model\Event::class)
            ->will($this->returnValue($group));

        $this->assertEquals($group, $this->groupRepository->updateGroup($groupIdentifier, $groupName));
    }

    public function testGetGroup()
    {
        $groupIdentifier = 5;
        $group = new \Microsoft\Graph\Model\Group();

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with('/groups/' . $groupIdentifier, \Microsoft\Graph\Model\Group::class)
            ->will($this->returnValue($group));

        $this->assertEquals($group, $this->groupRepository->getGroup($groupIdentifier));
    }

    public function testSubscribeOwnerInGroup()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $data = ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $office365UserIdentifier];

        $this->graphRepositoryMock->expects($this->once())
            ->method('executePostWithAccessTokenExpirationRetry')
            ->with('/groups/' . $groupIdentifier . '/owners/$ref', $data, \Microsoft\Graph\Model\Event::class);

        $this->groupRepository->subscribeOwnerInGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testRemoveOwnerFromGroup()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeDeleteWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier . '/$ref',
                \Microsoft\Graph\Model\Event::class
            );

        $this->groupRepository->removeOwnerFromGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testGetGroupOwner()
    {
        $user = new \Microsoft\Graph\Model\User();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier,
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->returnValue($user));

        $this->assertEquals(
            $user, $this->groupRepository->getGroupOwner($groupIdentifier, $office365UserIdentifier)
        );
    }

    public function testGetGroupOwnerWithResourceNotFoundException()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier,
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->throwException(new ClientExceptionStub(GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)));

        $this->groupRepository->getGroupOwner($groupIdentifier, $office365UserIdentifier);
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub
     */
    public function testGetGroupOwnerWithOtherException()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/owners/' . $office365UserIdentifier,
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->throwException(new ClientExceptionStub(301)));

        $this->groupRepository->getGroupOwner($groupIdentifier, $office365UserIdentifier);
    }

    public function testListGroupOwners()
    {
        $groupIdentifier = 5;

        $users = [new \Microsoft\Graph\Model\User()];

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/owners',
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->returnValue($users));

        $this->assertEquals($users, $this->groupRepository->listGroupOwners($groupIdentifier));
    }

    public function testSubscribeMemberInGroup()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $data = ['@odata.id' => 'https://graph.microsoft.com/v1.0/users/' . $office365UserIdentifier];

        $this->graphRepositoryMock->expects($this->once())
            ->method('executePostWithAccessTokenExpirationRetry')
            ->with('/groups/' . $groupIdentifier . '/members/$ref', $data, \Microsoft\Graph\Model\Event::class);

        $this->groupRepository->subscribeMemberInGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testRemoveMemberFromGroup()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeDeleteWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier . '/$ref',
                \Microsoft\Graph\Model\Event::class
            );

        $this->groupRepository->removeMemberFromGroup($groupIdentifier, $office365UserIdentifier);
    }

    public function testGetGroupMember()
    {
        $user = new \Microsoft\Graph\Model\User();

        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier,
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->returnValue($user));

        $this->assertEquals(
            $user, $this->groupRepository->getGroupMember($groupIdentifier, $office365UserIdentifier)
        );
    }

    public function testGetGroupMemberWithResourceNotFoundException()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier,
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->throwException(new ClientExceptionStub(GraphRepository::RESPONSE_CODE_RESOURCE_NOT_FOUND)));

        $this->groupRepository->getGroupMember($groupIdentifier, $office365UserIdentifier);
    }

    /**
     * @expectedException \Chamilo\Libraries\Protocol\Microsoft\Graph\Test\Stub\ClientExceptionStub
     */
    public function testGetGroupMemberWithOtherException()
    {
        $groupIdentifier = 5;
        $office365UserIdentifier = 8;

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/members/' . $office365UserIdentifier,
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->throwException(new ClientExceptionStub(301)));

        $this->groupRepository->getGroupMember($groupIdentifier, $office365UserIdentifier);
    }

    public function testListGroupMembers()
    {
        $groupIdentifier = 5;

        $users = [new \Microsoft\Graph\Model\User()];

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithAccessTokenExpirationRetry')
            ->with(
                '/groups/' . $groupIdentifier . '/members',
                \Microsoft\Graph\Model\User::class
            )
            ->will($this->returnValue($users));

        $this->assertEquals($users, $this->groupRepository->listGroupMembers($groupIdentifier));
    }

    public function testListGroupPlans()
    {
        $groupIdentifier = 5;

        $plans = [new \Microsoft\Graph\Model\PlannerPlan()];

        $this->graphRepositoryMock->expects($this->once())
            ->method('executeGetWithDelegatedAccess')
            ->with(
                '/groups/' . $groupIdentifier . '/planner/plans',
                \Microsoft\Graph\Model\PlannerPlan::class
            )
            ->will($this->returnValue($plans));

        $this->assertEquals($plans, $this->groupRepository->listGroupPlans($groupIdentifier));
    }

    public function testCreatePlanForGroup()
    {
        $groupIdentifier = 5;
        $planName = 'Planning in the Group 101';

        $plan = new \Microsoft\Graph\Model\PlannerPlan();

        $body = ['owner' => $groupIdentifier, 'title' => $planName];

        $this->graphRepositoryMock->expects($this->once())
            ->method('executePostWithDelegatedAccess')
            ->with(
                '/planner/plans',
                $body,
                \Microsoft\Graph\Model\PlannerPlan::class
            )
            ->will($this->returnValue($plan));


        $this->assertEquals($plan, $this->groupRepository->createPlanForGroup($groupIdentifier, $planName));
    }
}
