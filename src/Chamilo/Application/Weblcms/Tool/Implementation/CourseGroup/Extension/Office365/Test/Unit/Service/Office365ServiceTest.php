<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Test\Unit\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service\Office365Service;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 * Tests the Office365Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365ServiceTest extends ChamiloTestCase
{
    /**
     * @var Office365Service
     */
    protected $office365Service;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $office365RepositoryMock;

    /**
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localSettingMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->office365RepositoryMock = $this->getMockBuilder(Office365Repository::class)
            ->disableOriginalConstructor()->getMock();

        $this->localSettingMock = $this->getMockBuilder(LocalSetting::class)
            ->disableOriginalConstructor()->getMock();

        $this->office365Service = new Office365Service($this->office365RepositoryMock, $this->localSettingMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->office365RepositoryMock);
        unset($this->localSettingMock);
        unset($this->office365Service);
    }

    public function testCreateGroupByName()
    {
        $groupName = 'TestGroup 101';
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);

        $this->office365RepositoryMock->expects($this->once())
            ->method('createGroup')
            ->with($groupName)
            ->will($this->returnValue(new \Microsoft\Graph\Model\Group(['id' => 5])));

        $this->office365RepositoryMock->expects($this->once())
            ->method('subscribeMemberInGroup')
            ->with(5, $externalUserIdentifier);

        $this->assertEquals(5, $this->office365Service->createGroupByName($user, $groupName));
    }

    /**
     * @expectedException \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function testCreateGroupByNameWithInvalidUser()
    {
        $groupName = 'TestGroup 101';
        $user = new User();
        $this->mockGetExternalUserIdentifier($user, null);

        $this->office365Service->createGroupByName($user, $groupName);
    }

    public function testUpdateGroupName()
    {
        $groupName = 'TestGroup 101';
        $groupId = 5;

        $this->office365RepositoryMock->expects($this->once())
            ->method('updateGroup')
            ->with($groupId, $groupName);

        $this->office365Service->updateGroupName($groupId, $groupName);
    }

    public function testAddMemberToGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, null);

        $this->office365RepositoryMock->expects($this->once())
            ->method('subscribeMemberInGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->office365Service->addMemberToGroup($groupId, $user);
    }

    public function testAddMemberToGroupWhenAlreadyInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());
        $this->office365RepositoryMock->expects($this->never())
            ->method('subscribeMemberInGroup');

        $this->office365Service->addMemberToGroup($groupId, $user);
    }

    /**
     * @expectedException \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function testAddMemberToGroupWithInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user);
        $this->office365Service->addMemberToGroup($groupId, $user);
    }

    public function testRemoveMemberFromGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->office365RepositoryMock->expects($this->once())
            ->method('removeMemberFromGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->office365Service->removeMemberFromGroup($groupId, $user);
    }

    public function testRemoveMemberFromGroupNotInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, null);

        $this->office365RepositoryMock->expects($this->never())
            ->method('removeMemberFromGroup');

        $this->office365Service->removeMemberFromGroup($groupId, $user);
    }

    public function testRemoveMemberFromGroupInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user);

        $this->office365RepositoryMock->expects($this->never())
            ->method('removeMemberFromGroup');

        $this->office365Service->removeMemberFromGroup($groupId, $user);
    }

    public function testIsMemberOfGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->assertTrue($this->office365Service->isMemberOfGroup($groupId, $user));
    }

    public function testIsMemberOfGroupNotSubscribed()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, null);

        $this->assertFalse($this->office365Service->isMemberOfGroup($groupId, $user));
    }

    public function testIsMemberOfGroupInvalidUser()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupMember($groupId, $externalUserIdentifier, null);

        $this->assertFalse($this->office365Service->isMemberOfGroup($groupId, $user));
    }

    public function testGetGroupMembers()
    {
        $groupMembers = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
            new \Microsoft\Graph\Model\User(['id' => 6])
        ];

        $groupId = 5;

        $this->office365RepositoryMock->expects($this->once())
            ->method('listGroupMembers')
            ->with($groupId)
            ->will($this->returnValue($groupMembers));

        $this->assertEquals([4, 6], $this->office365Service->getGroupMembers($groupId));
    }

    public function testRemoveAllMembersFromGroup()
    {
        $groupMembers = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
        ];

        $groupId = 5;

        $this->office365RepositoryMock->expects($this->once())
            ->method('listGroupMembers')
            ->with($groupId)
            ->will($this->returnValue($groupMembers));

        $this->office365RepositoryMock->expects($this->once())
            ->method('removeMemberFromGroup')
            ->with($groupId, 4);

        $this->office365Service->removeAllMembersFromGroup($groupId);
    }

    public function testAddOwnerToGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, null);

        $this->office365RepositoryMock->expects($this->once())
            ->method('subscribeOwnerInGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->office365Service->addOwnerToGroup($groupId, $user);
    }

    public function testAddOwnerToGroupWhenAlreadyInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());
        $this->office365RepositoryMock->expects($this->never())
            ->method('subscribeMemberInGroup');

        $this->office365Service->addOwnerToGroup($groupId, $user);
    }

    /**
     * @expectedException \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function testAddOwnerToGroupWithInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user);
        $this->office365Service->addOwnerToGroup($groupId, $user);
    }

    public function testRemoveOwnerFromGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->office365RepositoryMock->expects($this->once())
            ->method('removeOwnerFromGroup')
            ->with($groupId, $externalUserIdentifier);

        $this->office365Service->removeOwnerFromGroup($groupId, $user);
    }

    public function testRemoveOwnerFromGroupNotInGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, null);

        $this->office365RepositoryMock->expects($this->never())
            ->method('removeOwnerFromGroup');

        $this->office365Service->removeOwnerFromGroup($groupId, $user);
    }

    public function testRemoveOwnerFromGroupInvalidUser()
    {
        $user = new User();
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user);

        $this->office365RepositoryMock->expects($this->never())
            ->method('removeOwnerFromGroup');

        $this->office365Service->removeOwnerFromGroup($groupId, $user);
    }

    public function testIsOwnerOfGroup()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, new \Microsoft\Graph\Model\User());

        $this->assertTrue($this->office365Service->isOwnerOfGroup($groupId, $user));
    }

    public function testIsOwnerOfGroupNotSubscribed()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, null);

        $this->assertFalse($this->office365Service->isOwnerOfGroup($groupId, $user));
    }

    public function testIsOwnerOfGroupInvalidUser()
    {
        $user = new User();
        $externalUserIdentifier = 'a987-asqfqsa-wvcaer465486';
        $groupId = 5;

        $this->mockGetExternalUserIdentifier($user, $externalUserIdentifier);
        $this->mockGetGroupOwner($groupId, $externalUserIdentifier, null);

        $this->assertFalse($this->office365Service->isOwnerOfGroup($groupId, $user));
    }

    public function testGetGroupOwners()
    {
        $groupOwners = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
            new \Microsoft\Graph\Model\User(['id' => 6])
        ];

        $groupId = 5;

        $this->office365RepositoryMock->expects($this->once())
            ->method('listGroupOwners')
            ->with($groupId)
            ->will($this->returnValue($groupOwners));

        $this->assertEquals([4, 6], $this->office365Service->getGroupOwners($groupId));
    }

    public function testRemoveAllOwnersFromGroup()
    {
        $groupOwners = [
            new \Microsoft\Graph\Model\User(['id' => 4]),
        ];

        $groupId = 5;

        $this->office365RepositoryMock->expects($this->once())
            ->method('listGroupOwners')
            ->with($groupId)
            ->will($this->returnValue($groupOwners));

        $this->office365RepositoryMock->expects($this->once())
            ->method('removeOwnerFromGroup')
            ->with($groupId, 4);

        $this->office365Service->removeAllOwnersFromGroup($groupId);
    }

    public function testGetGroupPlanIds()
    {
        $groupId = 5;

        $groupPlans = [
            new \Microsoft\Graph\Model\PlannerPlan(['id' => 3]),
            new \Microsoft\Graph\Model\PlannerPlan(['id' => 9]),
        ];

        $this->office365RepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue($groupPlans));

        $this->assertEquals([3, 9], $this->office365Service->getGroupPlanIds($groupId));
    }

    public function testGetDefaultGroupPlanId()
    {
        $groupId = 5;

        $groupPlans = [
            new \Microsoft\Graph\Model\PlannerPlan(['id' => 3]),
            new \Microsoft\Graph\Model\PlannerPlan(['id' => 9]),
        ];

        $this->office365RepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue($groupPlans));

        $this->assertEquals(3, $this->office365Service->getDefaultGroupPlanId($groupId));
    }

    public function testGetDefaultGroupPlanIdNoPlans()
    {
        $groupId = 5;
        $groupPlans = [];

        $this->office365RepositoryMock->expects($this->once())
            ->method('listGroupPlans')
            ->with($groupId)
            ->will($this->returnValue($groupPlans));

        $this->assertEmpty($this->office365Service->getDefaultGroupPlanId($groupId));
    }

    public function testGetOffice365UserIdentifier()
    {
        $user = new User();

        $this->mockGetExternalUserIdentifier($user, null);

        $this->office365RepositoryMock->expects($this->once())
            ->method('getOffice365User')
            ->with($user)
            ->will($this->returnValue(new \Microsoft\Graph\Model\User(['id' => 200])));

        $this->localSettingMock->expects($this->once())
            ->method('create')
            ->with(
                'external_user_id', 200,
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', $user
            );

        $this->assertEquals(200, $this->office365Service->getOffice365UserIdentifier($user));
    }

    public function testGetOffice365UserIdentifierFromCache()
    {
        $user = new User();

        $this->mockGetExternalUserIdentifier($user, 200);

        $this->office365RepositoryMock->expects($this->never())
            ->method('getOffice365User');

        $this->localSettingMock->expects($this->never())
            ->method('create');

        $this->assertEquals(200, $this->office365Service->getOffice365UserIdentifier($user));
    }

    public function testAuthorizeUserByAuthorizationCode()
    {
        $authorizationCode = 'VGhpcyBpcyBhbiBhdXRob3JpemF0aW9uIGNvZGU=';

        $this->office365RepositoryMock->expects($this->once())
            ->method('authorizeUserByAuthorizationCode')
            ->with($authorizationCode);

        $this->office365Service->authorizeUserByAuthorizationCode($authorizationCode);
    }

    /**
     * Mocks the getGroupMember function of the Office365Repository
     *
     * @param string $groupId
     * @param string $externalUserIdentifier
     * @param \Microsoft\Graph\Model\User $returnValue
     */
    protected function mockGetGroupMember($groupId, $externalUserIdentifier, $returnValue = null)
    {
        $this->office365RepositoryMock->expects($this->once())
            ->method('getGroupMember')
            ->with($groupId, $externalUserIdentifier)
            ->will($this->returnValue($returnValue));
    }

    /**
     * Mocks the getGroupMember function of the Office365Repository
     *
     * @param string $groupId
     * @param string $externalUserIdentifier
     * @param \Microsoft\Graph\Model\User $returnValue
     */
    protected function mockGetGroupOwner($groupId, $externalUserIdentifier, $returnValue = null)
    {
        $this->office365RepositoryMock->expects($this->once())
            ->method('getGroupOwner')
            ->with($groupId, $externalUserIdentifier)
            ->will($this->returnValue($returnValue));
    }

    /**
     * Mocks the get function of the local setting for the external_user_id
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $returnValue
     */
    protected function mockGetExternalUserIdentifier(User $user, $returnValue = null)
    {
        $this->localSettingMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(
                'external_user_id', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365',
                $user
            )
            ->will($this->returnValue($returnValue));
    }
}

