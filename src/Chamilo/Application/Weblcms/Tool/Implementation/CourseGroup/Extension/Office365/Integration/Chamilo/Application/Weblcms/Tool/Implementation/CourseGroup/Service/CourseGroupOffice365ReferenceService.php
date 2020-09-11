<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\Repository\CourseGroupOffice365ReferenceRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365ReferenceService
{
    /**
     * @var CourseGroupOffice365ReferenceRepository
     */
    protected $courseGroupOffice365ReferenceRepository;

    /**
     * CourseGroupOffice365ReferenceService constructor.
     * @param CourseGroupOffice365ReferenceRepository $courseGroupOffice365ReferenceRepository
     */
    public function __construct(CourseGroupOffice365ReferenceRepository $courseGroupOffice365ReferenceRepository)
    {
        $this->courseGroupOffice365ReferenceRepository = $courseGroupOffice365ReferenceRepository;
    }

    /**
     * Creates a new reference for a course group. If the group reference is already created the planner reference
     * (if changed) is updated
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $office365GroupId
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference
     *
     */
    public function createReferenceForCourseGroup(CourseGroup $courseGroup, $office365GroupId)
    {
        $courseGroupOffice365Reference = new CourseGroupOffice365Reference();
        $courseGroupOffice365Reference->setCourseGroupId($courseGroup->getId());
        $courseGroupOffice365Reference->setOffice365GroupId($office365GroupId);
        $courseGroupOffice365Reference->setLinked(true);

        if (!$this->courseGroupOffice365ReferenceRepository->createReference($courseGroupOffice365Reference))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a new CourseGroupOffice365Reference for course group %s', $courseGroup->getId()
                )
            );
        }

        return $courseGroupOffice365Reference;
    }

    /**
     * Returns whether or not the course group is connected to an office365 group (either linked or unlinked)
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return bool
     */
    public function courseGroupHasReference(CourseGroup $courseGroup)
    {
        $courseGroupOffice365Reference = $this->getCourseGroupReference($courseGroup);

        return $courseGroupOffice365Reference instanceof CourseGroupOffice365Reference;
    }

    /**
     * Returns the Office365 reference object for a course group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getCourseGroupReference(CourseGroup $courseGroup)
    {
        return $this->courseGroupOffice365ReferenceRepository->findByCourseGroup($courseGroup);
    }

    /**
     * Unlinks the course group from the office365 group. The reference object is never removed but
     * only flagged as unlinked so it can be retrieved in the future to reactivate the connection
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupOffice365Reference $courseGroupOffice365Reference
     *
     * @throws \RuntimeException
     */
    public function unlinkCourseGroupReference(CourseGroupOffice365Reference $courseGroupOffice365Reference)
    {
        $courseGroupOffice365Reference->setLinked(false);

        if (!$this->courseGroupOffice365ReferenceRepository->updateReference($courseGroupOffice365Reference))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the CourseGroupOffice365Reference for course group %s',
                    $courseGroupOffice365Reference->getCourseGroupId()
                )
            );
        }
    }
}
