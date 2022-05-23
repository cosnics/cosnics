<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Service;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\Repository\ActivityRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Storage\Query\OrderProperty;

/**
 * Service to manage activities of content objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ActivityService
{

    /**
     *
     * @var ActivityRepository
     */
    protected $activityRepository;

    /**
     * ActivityService constructor.
     *
     * @param ActivityRepository $activityRepository
     */
    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Counts the activities for a given content object
     *
     * @param ContentObject $contentObject
     *
     * @return int
     */
    public function countActivitiesForContentObject(ContentObject $contentObject)
    {
        $activitiesCount = 0;

        if ($contentObject instanceof ComplexContentObjectSupport)
        {
            $complex_content_object_path = $contentObject->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $activitiesCount += $this->activityRepository->countActivitiesForContentObject(
                    $node->get_content_object());
            }

            return $activitiesCount;
        }

        return $this->activityRepository->countActivitiesForContentObject($contentObject);
    }

    /**
     * Retrieves the activities for the given content object
     *
     * @param ContentObject $contentObject
     * @param int $offset
     * @param int $count
     * @param OrderProperty|null $orderBy
     *
     * @return Activity[]
     */
    public function retrieveActivitiesForContentObject(ContentObject $contentObject, $offset, $count,
        OrderProperty $orderBy = null)
    {
        $activities = [];

        if ($contentObject instanceof ComplexContentObjectSupport)
        {
            $complex_content_object_path = $contentObject->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $contentObjectActivities = $this->activityRepository->retrieveActivitiesForContentObject(
                    $node->get_content_object());

                foreach ($contentObjectActivities as $activity)
                {
                    $activity_instance = clone $activity;
                    $path = $node->get_fully_qualified_name(false, true);

                    if ($path)
                    {
                        $activity_instance->set_content(
                            $node->get_fully_qualified_name(false, true) . ' > ' . $activity_instance->get_content());
                    }

                    $activities[] = $activity_instance;
                }
            }
        }
        else
        {
            $activities = $this->activityRepository->retrieveActivitiesForContentObject($contentObject);

            foreach ($activities as $activity)
            {
                $activities[] = $activity;
            }
        }

        $orderBy = $orderBy[0];

        return $this->activityRepository->filterActivities($activities, $offset, $count, $orderBy);
    }
}