<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\TooManyUsersException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeamRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Microsoft\Graph\Model\Team;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlatformGroupTeamService
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository
     */
    protected $platformGroupTeamRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var TeamService
     */
    protected $teamService;

    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * PlatformGroupTeamService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository $platformGroupTeamRepository
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService $teamService
     * @param CourseService $courseService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\PlatformGroupTeamRepository $platformGroupTeamRepository,
        \Chamilo\Core\Group\Service\GroupService $groupService, \Chamilo\Core\User\Service\UserService $userService,
        \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService $teamService,
        CourseService $courseService
    )
    {
        $this->platformGroupTeamRepository = $platformGroupTeamRepository;
        $this->groupService = $groupService;
        $this->userService = $userService;
        $this->teamService = $teamService;
        $this->courseService = $courseService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $teamName
     * @param array $groupIds
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\TooManyUsersException
     */
    public function createTeamForSelectedGroups(User $owner, Course $course, string $teamName, $groupIds = [])
    {
        $userCount = 0;

        $groups = [];

        foreach ($groupIds as $groupId)
        {
            $group = $this->groupService->getGroupByIdentifier($groupId);

            if (!$group instanceof Group)
            {
                continue;
            }

            $groups[] = $group;

            $userCount += $group->count_users(true, true);
        }

        if ($userCount >= TeamService::MAX_USERS)
        {
            throw new TooManyUsersException();
        }

        $team = $this->teamService->createTeamByName($owner, $teamName);

        $platformGroupTeam = new PlatformGroupTeam();
        $platformGroupTeam->setCourseId($course->getId());
        $platformGroupTeam->setName($teamName);
        $platformGroupTeam->setTeamId($team->getId());

        if (!$this->platformGroupTeamRepository->createPlatformGroupTeam($platformGroupTeam))
        {
            throw new \RuntimeException(
                sprintf('The new team for the platform group (%s) could not be created', $teamName)
            );
        }

        foreach ($groups as $group)
        {
            $platformGroupTeamRelation = new PlatformGroupTeamRelation();
            $platformGroupTeamRelation->setGroupId($group->getId());
            $platformGroupTeamRelation->setPlatformGroupTeamId($platformGroupTeam->getId());

            if (!$this->platformGroupTeamRepository->createPlatformGroupTeamRelation($platformGroupTeamRelation))
            {
                throw new \RuntimeException(
                    'The relation between the team %s and the group %s could not be created'
                );
            }
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function addGroupUsersToPlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $groups = $this->platformGroupTeamRepository->findGroupsForPlatformGroupTeam($platformGroupTeam);
        $team = $this->getTeam($platformGroupTeam);
        $this->addGroupUsersToTeam($team, $groups->getArrayCopy());
    }

    /**
     * @param Course $course
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    public function removeTeamUsersNotInGroups(Course $course, PlatformGroupTeam $platformGroupTeam)
    {
        $groups = $this->platformGroupTeamRepository->findGroupsForPlatformGroupTeam($platformGroupTeam);
        $team = $this->getTeam($platformGroupTeam);

        $userIds = [];

        foreach ($groups as $group)
        {
            $userIds = array_merge($userIds, $group->get_users(true, true));
        }

        $users = $this->userService->findUsersByIdentifiers($userIds);

        /**
         * Make sure that the teachers are not removed
         */
        $teachers = $this->courseService->getTeachersFromCourse($course);
//        $this->teamService->removeTeamOwnersNotInArray($team, $teachers);
        $users = array_merge($users, $teachers);

        $this->teamService->removeTeamMembersNotInArray($team, $users);
    }

    /**
     * @param int $platformGroupTeamId
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlatformGroupTeamById(int $platformGroupTeamId)
    {
        return $this->platformGroupTeamRepository->findPlatformGroupTeamById($platformGroupTeamId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return string
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws NotAllowedException
     */
    public function getVisitTeamUrl(User $user, PlatformGroupTeam $platformGroupTeam)
    {
        $course = $this->courseService->getCourseById($platformGroupTeam->getCourseId());
        $isTeacher = $this->courseService->isUserTeacherInCourse($user, $course);
        if(!$isTeacher)
        {
            $allowed = false;

            $groupIds = $user->get_groups(true);
            $groups = $this->platformGroupTeamRepository->findGroupsForPlatformGroupTeam($platformGroupTeam);
            foreach($groups as $group)
            {
                if(in_array($group->getId(), $groupIds))
                {
                    $allowed = true;
                    break;
                }
            }

            if(!$allowed)
            {
                throw new NotAllowedException();
            }
        }

        $team = $this->getTeam($platformGroupTeam);

        if (!$team instanceof Team)
        {
            throw new \RuntimeException(
                sprintf(
                    'The given team with id %s could not be found and can therefor not be visited',
                    $platformGroupTeam->getTeamId()
                )
            );
        }

        if (!$this->teamService->isOwner($user, $team))
        {
            $this->teamService->addMember($user, $team);
        }

        return $team->getWebUrl();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @param User $user
     *
     * @return array
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     * @throws \Exception
     */
    public function getPlatformGroupTeamsForCourse(Course $course, User $user)
    {
        $isTeacher = $this->courseService->isUserTeacherInCourse($user, $course);
        $groupIds = [];
        if(!$isTeacher)
        {
            $groupIds = $user->get_groups(true);
        }

        $platformGroupTeamsData =
            $this->platformGroupTeamRepository->findPlatformGroupTeamsWithPlatformGroupsForCourse($course);

        $data = [];

        foreach ($platformGroupTeamsData as $row)
        {
            $id = $row[PlatformGroupTeam::PROPERTY_ID];

            if (!array_key_exists($id, $data))
            {
                if(!$isTeacher)
                {
                    if(!in_array($row[PlatformGroupTeamRepository::ALIAS_GROUP_ID], $groupIds))
                    {
                        continue;
                    }
                }

                $newTeamName = $this->updateTeamNameById(
                    $id, $row[PlatformGroupTeam::PROPERTY_TEAM_ID], $row[PlatformGroupTeam::PROPERTY_NAME]
                );

                if (!$newTeamName)
                {
                    continue;
                }

                $row[PlatformGroupTeam::PROPERTY_NAME] = $newTeamName;

                $data[$id] = ['id' => $id, 'name' => $row[PlatformGroupTeam::PROPERTY_NAME], 'groups' => []];
            }

            $data[$id]['groups'][] = [
                'name' => $row[PlatformGroupTeamRepository::ALIAS_GROUP_NAME],
                'code' => $row[PlatformGroupTeamRepository::ALIAS_GROUP_CODE]
            ];
        }

        return array_values($data);
    }

    /**
     * Helper method for the record iterator above. Cleans up deleted teams and updates team names when changed
     *
     * @param int $platformGroupId
     * @param string $teamId
     * @param string $currentName
     *
     * @return null|string
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    protected function updateTeamNameById(int $platformGroupId, string $teamId, string $currentName)
    {
        $team = $this->teamService->getTeam($teamId);
        if (!$team instanceof Team)
        {
            $platformGroupTeam = $this->findPlatformGroupTeamById($platformGroupId);
            $this->deleteRemovedTeam($platformGroupTeam);

            return null;
        }

        $teamName = $team->getProperties()['displayName'];

        if ($teamName != $currentName)
        {
            $platformGroupTeam = $this->findPlatformGroupTeamById($platformGroupId);
            $platformGroupTeam->setName($teamName);
            if (!$this->platformGroupTeamRepository->updatePlatformGroupTeam($platformGroupTeam))
            {
                throw new \RuntimeException(
                    'Could not update the platform group team with id ' . $platformGroupTeam->getId()
                );
            }
        }

        return $teamName;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     *
     * @return \Microsoft\Graph\Model\Team|null
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException
     */
    protected function getTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $team = $this->teamService->getTeam($platformGroupTeam->getTeamId());

        if (is_null($team))
        {
            $this->deletePlatformGroupTeam($platformGroupTeam);

            return null;
        }

        return $team;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam $platformGroupTeam
     */
    protected function deleteRemovedTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $this->platformGroupTeamRepository->deleteRelationsForPlatformGroupTeam($platformGroupTeam);
        $this->platformGroupTeamRepository->deletePlatformGroupTeam($platformGroupTeam);
    }

    /**
     * @param \Microsoft\Graph\Model\Team $team
     * @param array $groups
     */
    protected function addGroupUsersToTeam(Team $team, array $groups = [])
    {
        $userIds = [];

        foreach ($groups as $group)
        {
            $userIds = array_merge($userIds, $group->get_users(true, true));
        }

        $users = $this->userService->findUsersByIdentifiers($userIds);

        foreach ($users as $user)
        {
            try
            {
                $this->teamService->addMember($user, $team);
            }
            catch (AzureUserNotExistsException $exception)
            {
            }
        }
    }

    protected function deletePlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        throw new \RuntimeException('Not implemented');
    }
}