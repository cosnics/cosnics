<?php

namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage activities
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ActivityRepository extends CommonDataClassRepository
{
    /**
     * Counts the activities for a specific content object without keeping track of the activities of his children
     *
     * @param ContentObject $contentObject
     *
     * @return int
     */
    public function countActivitiesForContentObject(ContentObject $contentObject)
    {
        return $this->dataClassRepository->count(
            Activity::class_name(),
            new DataClassCountParameters($this->getActivityConditionForContentObject($contentObject))
        );
    }

    /**
     * Retrieves the activities for a specific content object without keeping track of the activities of his children
     *
     * @param ContentObject $contentObject
     *
     * @return Activity[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieveActivitiesForContentObject(ContentObject $contentObject)
    {
        return $this->dataClassRepository->retrieves(
            Activity::class_name(),
            new DataClassRetrievesParameters($this->getActivityConditionForContentObject($contentObject))
        );
    }

    /**
     * @param Activity[] $activities
     * @param $offset
     * @param $count
     * @param $orderBy
     *
     * @return Activity[]
     */
    public function filterActivities($activities, $offset = null, $count = null, OrderBy $orderBy = null)
    {
        usort(
            $activities,
            function (Activity $activity_a, Activity $activity_b) use ($orderBy) {
                switch ($orderBy->get_property()->get_property())
                {
                    case Activity::PROPERTY_TYPE :
                        if ($orderBy->get_direction() == SORT_ASC)
                        {
                            return strcmp($activity_a->get_type_string(), $activity_b->get_type_string());
                        }
                        else
                        {
                            return strcmp($activity_b->get_type_string(), $activity_a->get_type_string());
                        }
                        break;
                    case Activity::PROPERTY_CONTENT :
                        if ($orderBy->get_direction() == SORT_ASC)
                        {
                            return strcmp($activity_a->get_content(), $activity_b->get_content());
                        }
                        else
                        {
                            return strcmp($activity_b->get_content(), $activity_a->get_content());
                        }
                        break;
                    case Activity::PROPERTY_DATE :
                        if ($orderBy->get_direction() == SORT_ASC)
                        {
                            return ($activity_a->get_date() < $activity_b->get_date()) ? - 1 : 1;
                        }
                        else
                        {
                            return ($activity_a->get_date() > $activity_b->get_date()) ? - 1 : 1;
                        }
                        break;
                }

                return 1;
            }
        );

        return array_splice($activities, $offset, $count);
    }

    /**
     * @param ContentObject $contentObject
     *
     * @return EqualityCondition
     */
    protected function getActivityConditionForContentObject(ContentObject $contentObject)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Activity::class_name(), Activity::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObject->getId())
        );
    }
}