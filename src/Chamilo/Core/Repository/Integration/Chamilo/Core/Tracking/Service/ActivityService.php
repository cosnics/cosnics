<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Service;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\Repository\ActivityRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityService
{
    protected ActivityRepository $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function countActivitiesForContentObject(ContentObject $contentObject): int
    {
        $activitiesCount = 0;

        if ($contentObject instanceof ComplexContentObjectSupportInterface)
        {
            $complex_content_object_path = $contentObject->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $activitiesCount += $this->getActivityRepository()->countActivitiesForContentObject(
                    $node->get_content_object()
                );
            }

            return $activitiesCount;
        }

        return $this->getActivityRepository()->countActivitiesForContentObject($contentObject);
    }

    public function getActivityRepository(): ActivityRepository
    {
        return $this->activityRepository;
    }

    /**
     * @param ContentObject $contentObject
     * @param ?int $offset
     * @param ?int $count
     * @param ?OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Event\Activity>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveActivitiesForContentObject(
        ContentObject $contentObject, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $activities = [];

        if ($contentObject instanceof ComplexContentObjectSupportInterface)
        {
            $complex_content_object_path = $contentObject->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $contentObjectActivities = $this->getActivityRepository()->retrieveActivitiesForContentObject(
                    $node->get_content_object()
                );

                foreach ($contentObjectActivities as $activity)
                {
                    $activity_instance = clone $activity;
                    $path = $node->get_fully_qualified_name(false, true);

                    if ($path)
                    {
                        $activity_instance->set_content(
                            $node->get_fully_qualified_name(false, true) . ' > ' . $activity_instance->get_content()
                        );
                    }

                    $activities[] = $activity_instance;
                }
            }
        }
        else
        {
            $activities = $this->getActivityRepository()->retrieveActivitiesForContentObject($contentObject);

            foreach ($activities as $activity)
            {
                $activities[] = $activity;
            }
        }

        $orderBy = $orderBy[0];

        $filteredActivities = $this->getActivityRepository()->filterActivities($activities, $offset, $count, $orderBy);

        return new ArrayCollection($filteredActivities);
    }
}