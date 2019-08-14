<?php

namespace Chamilo\Core\Group\Test\Integration;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\Repository\GroupRepository;
use Chamilo\Core\Group\Storage\Repository\GroupSubscriptionRepository;
use Chamilo\Core\User\Service\PasswordSecurity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloFixturesBasedTestCase;
use Chamilo\Libraries\Platform\Session\SessionUtilities;

/**
 * Tests the group closure table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupClosureTableTest extends ChamiloFixturesBasedTestCase
{
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var GroupSubscriptionService
     */
    protected $groupSubscriptionService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ExceptionLoggerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $exceptionLoggerMock;

    /**
     * Setup before each test
     */
    protected function setUp()
    {
        parent::setUp();

        $groupSubscriptionRepository = new GroupSubscriptionRepository($this->getTestDataClassRepository());

        $this->groupService = new GroupService(
            new GroupRepository($this->getTestDataClassRepository(), $groupSubscriptionRepository)
        );

        $this->exceptionLoggerMock = $this->getMockBuilder(ExceptionLoggerInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->groupSubscriptionService =
            new GroupSubscriptionService($groupSubscriptionRepository, $this->groupService, $this->exceptionLoggerMock);

        $this->groupService->setGroupSubscriptionService($this->groupSubscriptionService);

        $this->userService = new UserService(
            new UserRepository(), $this->getService(PasswordSecurity::class),
            $this->getService('chamilo.libraries.platform.session.session_utilities')
        );
    }

    /**
     * Teardown after each test
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Returns the storage units that need to be created. This method requires a multidimensional array with the
     * names of the storage units per context
     *
     * [ $context => [$storageUnit1, $storageUnit2] ]
     *
     * @return array
     */
    protected function getStorageUnitsToCreate()
    {
        return [
            'Chamilo\Core\Group' => ['group', 'group_closure_table', 'group_rel_user'],
            'Chamilo\Core\User' => ['user']
        ];
    }

    /**
     * Returns the fixture files that need to be inserted. This method requires a multidimensional array with the
     * names of the fixture files per context
     *
     * [ $context => [$fixtureFileName1, $fixtureFileName2] ]
     *
     * @return array
     */
    protected function getFixtureFiles()
    {
        return [
            'Chamilo\Core\Group' => ['Group', 'GroupClosureTable', 'GroupRelUser'],
            'Chamilo\Core\User' => ['User']
        ];
    }

    public function testGetAllChildIdsFromRootGroup()
    {
        $rootGroup = $this->groupService->getRootGroup();
        $childIds = $this->groupService->getAllChildIdsForGroup($rootGroup);

        $this->assertCount(6, $childIds);
    }

    public function testGetAllChildIdsFromSubgroup()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(5);
        $childIds = $this->groupService->getAllChildIdsForGroup($subGroup);

        $this->assertCount(2, $childIds);
        $this->assertEquals([5, 6], $childIds);
    }

    public function testGetAllChildIdsFromSubgroupExcludeSelf()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(5);
        $this->assertCount(1, $this->groupService->getAllChildIdsForGroup($subGroup, false));
    }

    public function testGetAllChildrenForGroup()
    {
        $rootGroup = $this->groupService->getRootGroup();
        $children = $this->groupService->getAllChildrenForGroup($rootGroup);

        $this->assertCount(6, $children);
    }

    public function testGetAllChildrenForGroupFromSubgroup()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(5);
        $this->assertCount(2, $this->groupService->getAllChildrenForGroup($subGroup));
    }

    public function testGetAllChildrenForGroupFromSubgroupExcludeSelf()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(5);
        $this->assertCount(1, $this->groupService->getAllChildrenForGroup($subGroup, false));
    }

    public function testFindDirectChildrenFromGroup()
    {
        $rootGroup = $this->groupService->getRootGroup();
        $directChildren = $this->groupService->findDirectChildrenFromGroup($rootGroup);

        $this->assertCount(4, $directChildren);
    }

    public function testGetAllParentsForGroup()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(6);
        $this->assertCount(3, $this->groupService->getAllParentsForGroup($subGroup));
    }

    public function testGetAllParentsForGroupExcludeSelf()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(6);
        $this->assertCount(2, $this->groupService->getAllParentsForGroup($subGroup, false));
    }

    public function testGetAllParentIdsForGroup()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(6);
        $parentIds = $this->groupService->getAllParentIdsForGroup($subGroup);
        $this->assertCount(3, $parentIds);
        $this->assertEquals([1, 5, 6], $parentIds);
    }

    public function testGetAllParentIdsForGroupExcludeSelf()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(6);
        $this->assertCount(2, $this->groupService->getAllParentIdsForGroup($subGroup, false));
    }

    public function testGetDirectParentForGroup()
    {
        $subGroup = $this->groupService->getGroupByIdentifier(6);
        $this->assertEquals(5, $this->groupService->getDirectParentOfGroup($subGroup)->getId());
    }

    public function testCreateGroup()
    {
        $group = new Group();
        $group->set_name('TestSubGroup500');
        $group->set_parent_id(6);
        $group->set_code(strtoupper($group->get_name()));

        $this->groupService->createGroup($group);

        $this->assertCount(4, $this->groupService->getAllParentIdsForGroup($group));
    }

    public function testUpdateGroup()
    {
        $group = $this->groupService->getGroupByIdentifier(6);
        $group->set_name('ChangedGroupName');

        $this->groupService->updateGroup($group);

        $this->getTestDataClassRepository()->getDataClassRepositoryCache()->truncate(Group::class);

        $group = $this->groupService->getGroupByIdentifier(6);
        $this->assertEquals('ChangedGroupName', $group->get_name());
    }

    public function testDeleteGroup()
    {
        $group = $this->groupService->getGroupByIdentifier(6);
        $this->groupService->deleteGroup($group);

        $parentGroup = $this->groupService->getGroupByIdentifier(5);

        $this->assertCount(0, $this->groupService->getAllChildIdsForGroup($parentGroup, false));
    }

    public function testMoveGroup()
    {
        $rootGroup = $this->groupService->getRootGroup();

        $group = $this->groupService->getGroupByIdentifier(5);
        $this->groupService->moveGroup($group, 2);

        $leafGroup = $this->groupService->getGroupByIdentifier(6);
        $this->assertEquals([1, 2, 5, 6], $this->groupService->getAllParentIdsForGroup($leafGroup));
        $this->assertCount(3, $this->groupService->findDirectChildrenFromGroup($rootGroup));
    }

    public function testFindGroupByCode()
    {
        $group = $this->groupService->findGroupByCode('COSNICS');
        $this->assertEquals(1, $group->getId());
    }

    public function testAddGroupToClosureTable()
    {
        $group = new Group();
        $group->set_name('TestSubGroup500');
        $group->set_parent_id(6);
        $group->set_code(strtoupper($group->get_name()));
        $group->create();

        $this->groupService->addGroupToClosureTable($group);

        $this->assertCount(4, $this->groupService->getAllParentIdsForGroup($group));
    }

    public function testFindGroupUserRelation()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getGroupByIdentifier(2);

        $this->assertInstanceOf(
            GroupRelUser::class, $this->groupSubscriptionService->findGroupUserRelation($group, $user)
        );
    }

    public function testSubscribeUserToGroupByCode()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getRootGroup();

        $this->groupSubscriptionService->subscribeUserToGroupByCode('COSNICS', $user);

        $this->assertInstanceOf(
            GroupRelUser::class, $this->groupSubscriptionService->findGroupUserRelation($group, $user)
        );
    }

    public function testSubscribeUserToGroup()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getRootGroup();

        $this->groupSubscriptionService->subscribeUserToGroup($group, $user);

        $this->assertInstanceOf(
            GroupRelUser::class, $this->groupSubscriptionService->findGroupUserRelation($group, $user)
        );
    }

    public function testRemoveUserFromGroupByCode()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getGroupByIdentifier(2);

        $this->groupSubscriptionService->removeUserFromGroupByCode($group->get_code(), $user);

        $this->assertFalse($this->groupSubscriptionService->findGroupUserRelation($group, $user));
    }

    public function testRemoveUserFromGroup()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getGroupByIdentifier(2);

        $this->groupSubscriptionService->removeUserFromGroup($group, $user);

        $this->assertFalse($this->groupSubscriptionService->findGroupUserRelation($group, $user));
    }

    public function testFindUsersDirectlySubscribedToGroup()
    {
        $group = $this->groupService->getGroupByIdentifier(6);
        $this->assertCount(2, $this->groupSubscriptionService->findUsersDirectlySubscribedToGroup($group));
    }

    public function testFindUserIdsDirectlySubscribedToGroup()
    {
        $group = $this->groupService->getGroupByIdentifier(6);
        $this->assertCount(2, $this->groupSubscriptionService->findUserIdsDirectlySubscribedToGroup($group));
    }

    public function testFindUsersInGroupAndSubgroups()
    {
        $group = $this->groupService->getGroupByIdentifier(1);
        $this->assertCount(3, $this->groupSubscriptionService->findUsersInGroupAndSubgroups($group));
    }

    public function testFindUserIdsInGroupAndSubgroups()
    {
        $group = $this->groupService->getGroupByIdentifier(1);
        $this->assertCount(3, $this->groupSubscriptionService->findUserIdsInGroupAndSubgroups($group));
    }

    public function testFindGroupsWhereUserIsDirectlySubscribed()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $this->assertCount(3, $this->groupSubscriptionService->findGroupsWhereUserIsDirectlySubscribed($user));
    }

    public function testFindGroupIdsWhereUserIsDirectlySubscribed()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $this->assertCount(3, $this->groupSubscriptionService->findGroupIdsWhereUserIsDirectlySubscribed($user));
    }

    public function testFindAllGroupsForUser()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $this->assertCount(5, $this->groupSubscriptionService->findAllGroupsForUser($user));
    }

    /**
     * THIS TEST CAN CURRENTLY NOT WORK WITH THE DEFAULT METHOD BECAUSE IT STILL REFERS TO THE OLD DATAMANAGER.
     * TO MAKE THIS TESTABLE I'VE CREATED A TEMPORARY METHOD USING ONLY THE NEW METHOD.
     */
    public function testFindAllGroupIdsForUser()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $groupIds = $this->groupSubscriptionService->findAllGroupIdsForUserTempNew($user);

        $this->assertCount(5, $groupIds);
        $this->assertEquals([1, 5, 2, 3, 6], $groupIds);
    }

    public function testIsUserDirectlySubscribedToGroup()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getGroupByIdentifier(6);

        $this->assertTrue($this->groupSubscriptionService->isUserDirectlySubscribedToGroup($group, $user));
    }

    public function testIsUserDirectlySubscribedToGroupReturnsFalse()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getGroupByIdentifier(5);

        $this->assertFalse($this->groupSubscriptionService->isUserDirectlySubscribedToGroup($group, $user));
    }

    public function testIsUserSubscribedToGroupOrSubgroups()
    {
        $user = $this->userService->findUserByIdentifier(1);
        $group = $this->groupService->getGroupByIdentifier(5);

        $this->assertTrue($this->groupSubscriptionService->isUserSubscribedToGroupOrSubgroups($group, $user));
    }
}

