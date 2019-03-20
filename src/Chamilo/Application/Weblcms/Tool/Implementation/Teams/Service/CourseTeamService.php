<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception\CourseTeamAlreadyExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\CourseTeamRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\CourseTeamRelationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
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
     * @param CourseTeamRelationRepository $courseTeamRelationRepository
     * @param TeamService $teamService
     * @param CourseServiceInterface $courseService
     */
    public function __construct(
        CourseTeamRelationRepository $courseTeamRelationRepository,
        TeamService $teamService,
        CourseServiceInterface $courseService)
    {
        $this->courseTeamRelationRepository = $courseTeamRelationRepository;
        $this->teamService = $teamService;
        $this->courseService = $courseService;
    }

    /**
     * @param User $owner
     * @param Course $course
     * @return Team
     * @throws CourseTeamAlreadyExistsException
     * @throws AzureUserNotExistsException
     * @throws GraphException
     */
    public function createTeam(User $owner, Course $course): Team
    {
        if(!is_null($this->getTeam($course))) {
            throw new CourseTeamAlreadyExistsException($course);
        }

        $team = $this->teamService->createTeamByName($owner, $course->get_title());

        $courseTeamRelation = new CourseTeamRelation();
        $courseTeamRelation->setCourseId($course->getId());
        $courseTeamRelation->setTeamId($team->getId());

        if (!$this->courseTeamRelationRepository->create($courseTeamRelation))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a new CourseTeam for course %s', $course->getId()
                )
            );
        }

        return $team;
    }

    /**
     * @param Course $course
     * @return Team|null
     * @throws GraphException
     */
    public function getTeam(Course $course): ?Team
    {
        $courseTeamRelation = $this->courseTeamRelationRepository->findByCourse($course);

        if(!$courseTeamRelation instanceof CourseTeamRelation) {
            return null;
        }

        $team = $this->teamService->getTeam($courseTeamRelation->getTeamId());

        if(is_null($team)) {
            //team was deleted
            $this->courseTeamRelationRepository->delete($courseTeamRelation);
            return null;
        }
        return $team;
    }

    /**
     * @param string $teamId
     * @return Course|null
     */
    public function getCourseByTeamId(string $teamId): ?Course
    {
        $courseTeamRelation = $this->courseTeamRelationRepository->findByTeamId($teamId);

        if(!$courseTeamRelation instanceof CourseTeamRelation) {
            return null;
        }

        $course = $this->courseService->getCourseById($courseTeamRelation->getCourseId());

        if(! $course instanceof Course) {
            return null;
        }

        return $course;
    }

    /**
     * @param Course $course
     * @throws GraphException
     */
    public function removeTeamUsersNotInCourse(Course $course)
    {
        $this->teamService->removeTeamOwnersNotInArray(
            $this->getTeam($course),
            $this->courseService->getTeachersFromCourse($course)
        );

        $this->teamService->removeTeamMembersNotInArray(
            $this->getTeam($course),
            $this->courseService->getStudentsFromCourse($course)
        );
    }

    /**
     * @param Course $course
     * @throws GraphException
     * @throws AzureUserNotExistsException
     */
    public function addAllCourseUsersToTeam(Course $course)
    {
        $this->addCourseStudentsToTeam($course);
        $this->addCourseTeachersToTeam($course);
    }

    /**
     * @param Course $course
     * @throws GraphException
     * @throws AzureUserNotExistsException
     */
    public function addCourseStudentsToTeam(Course $course)
    {
        $team = $this->getTeam($course);
        if(is_null($team)) {
            return;
        }

        $students = $this->courseService->getStudentsFromCourse($course);
        foreach ($students as $student) {
            $this->teamService->addMember($student, $team);
        }
    }

    /**
     * @param Course $course
     * @throws GraphException
     * @throws AzureUserNotExistsException
     */
    public function addCourseTeachersToTeam(Course $course)
    {
        $team = $this->getTeam($course);
        if(is_null($team)) {
            return;
        }

        $teachers = $this->courseService->getTeachersFromCourse($course);
        foreach ($teachers as $teacher) {
            $this->teamService->addOwner($teacher, $team);
        }
    }

    /**
     * @param Course $course
     */
    public function removeTeam(Course $course)
    {
        throw new \RuntimeException(
            sprintf(
                'Not yet implemented!'
            )
        );
    }
}