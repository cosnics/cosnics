<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\CourseTeamRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\Repository\CourseTeamRelationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\TeamService;
use Microsoft\Graph\Model\Team;

/**
 * Class CourseTeamService
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
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
     * CourseTeamService constructor.
     * @param CourseTeamRelationRepository $courseTeamRelationRepository
     * @param TeamService $teamService
     */
    public function __construct(CourseTeamRelationRepository $courseTeamRelationRepository, TeamService $teamService)
    {
        $this->courseTeamRelationRepository = $courseTeamRelationRepository;
        $this->teamService = $teamService;
    }

    /**
     * @param User $owner
     * @param Course $course
     * @return Team
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createTeam(User $owner, Course $course): Team
    {
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
     * @return Team
     * @throws ObjectNotExistException
     */
    public function getTeam(Course $course): Team
    {
        $courseTeamRelation = $this->courseTeamRelationRepository->findByCourse($course);

        if(!$courseTeamRelation instanceof CourseTeamRelation) {
            throw new ObjectNotExistException(Translation::get('CourseTeamRelation'), $course->getId());
        }

        return $this->teamService->getTeam($courseTeamRelation->getTeamId());
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