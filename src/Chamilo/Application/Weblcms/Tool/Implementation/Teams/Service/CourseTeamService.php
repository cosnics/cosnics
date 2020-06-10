<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\CourseTeamAlreadyExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\CourseTeamNotExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\TooManyUsersException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\CourseTeamRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\CourseTeamRelationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\TeamNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Microsoft\Graph\Model\Team;

/**
 * Class CourseTeamService
 */
class CourseTeamService
{
    /**
     * @var CourseTeamRelationRepository
     */
    protected $courseTeamRelationRepository;

    /**
     * @var TeamService
     */
    protected $teamService;

    /**
     * @var CourseServiceInterface
     */
    protected $courseService;

    /**
     * CourseTeamService constructor.
     *
     * @param CourseTeamRelationRepository $courseTeamRelationRepository
     * @param TeamService $teamService
     * @param CourseServiceInterface $courseService
     */
    public function __construct(
        CourseTeamRelationRepository $courseTeamRelationRepository,
        TeamService $teamService,
        CourseServiceInterface $courseService
    )
    {
        $this->courseTeamRelationRepository = $courseTeamRelationRepository;
        $this->teamService = $teamService;
        $this->courseService = $courseService;
    }

    /**
     * @param User $owner
     * @param Course $course
     *
     * @return string
     * @throws CourseTeamAlreadyExistsException
     * @throws GraphException
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\TooManyUsersException
     * @throws AzureUserNotExistsException
     */
    public function createTeam(User $owner, Course $course): string
    {
        if (!is_null($this->getTeam($course)))
        {
            throw new CourseTeamAlreadyExistsException($course);
        }

        $students = $this->courseService->getStudentsFromCourse($course);
        $teachers = $this->courseService->getTeachersFromCourse($course);

        $userCount = count($students) + count($teachers);
        if ($userCount >= TeamService::MAX_USERS)
        {
            throw new TooManyUsersException();
        }

        $teamName = $course->get_title() . ' (' . $course->get_visual_code() . ')';
        $teamId = $this->teamService->createClassTeam($teamName, $teamName, $owner);

        $courseTeamRelation = new CourseTeamRelation();
        $courseTeamRelation->setCourseId($course->getId());
        $courseTeamRelation->setTeamId($teamId);

        if (!$this->courseTeamRelationRepository->create($courseTeamRelation))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a new CourseTeam for course %s', $course->getId()
                )
            );
        }

        return $teamId;
    }

    /**
     * @param Course $course
     *
     * @return Team|null
     */
    public function getTeam(Course $course): ?Team
    {
        $courseTeamRelation = $this->courseTeamRelationRepository->findByCourse($course);

        if (!$courseTeamRelation instanceof CourseTeamRelation)
        {
            return null;
        }

        try
        {
            return $this->teamService->getTeam($courseTeamRelation->getTeamId());
        }
        catch(GraphException $ex)
        {
            return null;
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function courseHasTeam(Course $course)
    {
        $courseTeamRelation = $this->courseTeamRelationRepository->findByCourse($course);

        return $courseTeamRelation instanceof CourseTeamRelation;
    }

    /**
     * @param Course $course
     *
     * @throws CourseTeamNotExistsException
     * @throws GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException
     */
    public function removeTeamUsersNotInCourse(Course $course)
    {
        $teachers = $this->courseService->getTeachersFromCourse($course);

        $team = $this->getTeam($course);
        if (!$team)
        {
            throw new CourseTeamNotExistsException($course);
        }

        $this->teamService->removeTeamOwnersNotInArray($team, $teachers);

        $students = $this->courseService->getStudentsFromCourse($course);
        $studentsAndTeachers = array_merge($teachers, $students);

        $this->teamService->removeTeamMembersNotInArray(
            $team, $studentsAndTeachers

        );
    }

    /**
     * @param Course $course
     *
     * @throws AzureUserNotExistsException
     * @throws CourseTeamNotExistsException
     * @throws GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function addAllCourseUsersToTeam(Course $course)
    {
        $this->addCourseStudentsToTeam($course);
        $this->addCourseTeachersToTeam($course);
    }

    /**
     * @param Course $course
     *
     * @throws AzureUserNotExistsException
     * @throws CourseTeamNotExistsException
     * @throws GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UnknownAzureUserIdException
     */
    public function addCourseStudentsToTeam(Course $course)
    {
        $team = $this->getTeam($course);
        if (is_null($team))
        {
            throw new CourseTeamNotExistsException($course);
        }

        $students = $this->courseService->getStudentsFromCourse($course);
        foreach ($students as $student)
        {
            $this->teamService->addMember($student, $team);
        }
    }

    /**
     * @param Course $course
     *
     * @throws AzureUserNotExistsException
     * @throws CourseTeamNotExistsException
     * @throws GraphException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GroupNotExistsException
     */
    public function addCourseTeachersToTeam(Course $course)
    {
        $team = $this->getTeam($course);
        if (is_null($team))
        {
            throw new CourseTeamNotExistsException($course);
        }

        $teachers = $this->courseService->getTeachersFromCourse($course);
        foreach ($teachers as $teacher)
        {
            $this->teamService->addOwner($teacher, $team);
        }
    }

    /**
     * @param Course $course
     */
    public function removeTeam(Course $course)
    {
        $courseTeamRelation = $this->courseTeamRelationRepository->findByCourse($course);

        if (!$courseTeamRelation instanceof CourseTeamRelation)
        {
            return;
        }

        $courseTeamRelation->setActive(false);

        if (!$this->courseTeamRelationRepository->updateCourseTeamRelation($courseTeamRelation))
        {
            throw new \RuntimeException('Could not deactivate the course team relation in the database');
        }
    }
}
