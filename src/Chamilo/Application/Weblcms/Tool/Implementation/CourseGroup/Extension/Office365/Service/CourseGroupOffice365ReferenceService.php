<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\DataClass\CourseGroupOffice365Reference;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupOffice365ReferenceService
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\CourseGroupOffice365ReferenceRepository
     */
    protected $courseGroupOffice365ReferenceRepository;

    /**
     * CourseGroupOffice365Service constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\CourseGroupOffice365ReferenceRepository $courseGroupOffice365ReferenceRepository
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\CourseGroupOffice365ReferenceRepository $courseGroupOffice365ReferenceRepository
    )
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
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\DataClass\CourseGroupOffice365Reference
     *
     * @throws \Exception
     */
    public function createReferenceForCourseGroup(CourseGroup $courseGroup, $office365GroupId)
    {
        $courseGroupOffice365Reference = new CourseGroupOffice365Reference();
        $courseGroupOffice365Reference->setCourseGroupId($courseGroup->getId());
        $courseGroupOffice365Reference->setOffice365GroupId($office365GroupId);

        if (!$this->courseGroupOffice365ReferenceRepository->createReference($courseGroupOffice365Reference))
        {
            throw new \Exception(
                sprintf(
                    'Could not create a new CourseGroupOffice365Reference for course group %s', $courseGroup->getId()
                )
            );
        }

        return $courseGroupOffice365Reference;
    }

    /**
     * Removes the reference for a course group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @throws \Exception
     */
    public function removeReferenceForCourseGroup(CourseGroup $courseGroup)
    {
        if(!$this->courseGroupOffice365ReferenceRepository->removeReferenceForCourseGroup($courseGroup))
        {
            throw new \Exception(
                sprintf(
                    'Could not create the CourseGroupOffice365Reference for course group %s', $courseGroup->getId()
                )
            );
        }
    }

    /**
     * Returns whether or not the course group is connected to an office365 group
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
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\DataClass\CourseGroupOffice365Reference|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getCourseGroupReference(CourseGroup $courseGroup)
    {
        return $this->courseGroupOffice365ReferenceRepository->findByCourseGroup($courseGroup);
    }

    /**
     * Stores a planner reference for a course group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $office365GroupId
     * @param string $office365PlanId
     *
     * @throws \Exception
     */
    public function storePlannerReferenceForCourseGroup(CourseGroup $courseGroup, $office365GroupId, $office365PlanId)
    {
        $courseGroupOffice365Reference = $this->courseGroupOffice365ReferenceRepository->findByCourseGroup($courseGroup);
        if(!$courseGroupOffice365Reference)
        {
            $courseGroupOffice365Reference = $this->createReferenceForCourseGroup($courseGroup, $office365GroupId);
        }

        if (!empty($office365PlanId) && $office365PlanId != $courseGroupOffice365Reference->getOffice365PlanId())
        {
            $courseGroupOffice365Reference->setOffice365PlanId($office365PlanId);

            if (!$this->courseGroupOffice365ReferenceRepository->updateReference($courseGroupOffice365Reference))
            {
                throw new \Exception(
                    sprintf(
                        'Could not update the CourseGroupOffice365Reference for course group %s', $courseGroup->getId()
                    )
                );
            }
        }
    }

    /**
     * Removes the planner reference from the course group reference
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @throws \Exception
     */
    public function removePlannerFromCourseGroup(CourseGroup $courseGroup)
    {
        $courseGroupOffice365Reference = $this->courseGroupOffice365ReferenceRepository->findByCourseGroup($courseGroup);
        if(!$courseGroupOffice365Reference instanceof CourseGroupOffice365Reference)
        {
            throw new \InvalidArgumentException('The given course group is not connected to an office365 group');
        }

        $courseGroupOffice365Reference->setOffice365PlanId(null);

        if (!$this->courseGroupOffice365ReferenceRepository->updateReference($courseGroupOffice365Reference))
        {
            throw new \Exception(
                sprintf(
                    'Could not create a new CourseGroupOffice365Reference for course group %s', $courseGroup->getId()
                )
            );
        }

        /*
         * Removes the reference to the course group because there is no use to store
         * the reference (currently) if planner is removed
         */
        $this->removeReferenceForCourseGroup($courseGroup);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return bool
     */
    public function courseGroupHasPlannerReference(CourseGroup $courseGroup)
    {
        $courseGroupOffice365Reference = $this->courseGroupOffice365ReferenceRepository->findByCourseGroup($courseGroup);
        if(!$courseGroupOffice365Reference instanceof CourseGroupOffice365Reference)
        {
            return false;
        }

        return !empty($courseGroupOffice365Reference->getOffice365PlanId());
    }
}