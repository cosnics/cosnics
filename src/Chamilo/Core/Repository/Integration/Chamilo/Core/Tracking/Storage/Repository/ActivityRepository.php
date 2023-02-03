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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Repository to manage activities
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityRepository extends CommonDataClassRepository
{

    public function countActivitiesForContentObject(ContentObject $contentObject): int
    {
        return $this->dataClassRepository->count(
            Activity::class, new DataClassCountParameters($this->getActivityConditionForContentObject($contentObject))
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity[] $activities
     * @param ?int $offset
     * @param ?int $count
     * @param ?OrderBy $orderBy
     *
     * @return \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity[]
     */
    public function filterActivities(
        array $activities, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): array
    {
        if ($orderBy instanceof OrderBy)
        {
            usort(
                $activities, function (Activity $activity_a, Activity $activity_b) use ($orderBy) {

                $orderProperty = $orderBy->getFirst();

                switch ($orderProperty->getConditionVariable()->getPropertyName())
                {
                    case Activity::PROPERTY_TYPE :
                        if ($orderProperty->getDirection() == SORT_ASC)
                        {
                            return strcmp($activity_a->get_type_string(), $activity_b->get_type_string());
                        }
                        else
                        {
                            return strcmp($activity_b->get_type_string(), $activity_a->get_type_string());
                        }
                    case Activity::PROPERTY_CONTENT :
                        if ($orderProperty->getDirection() == SORT_ASC)
                        {
                            return strcmp($activity_a->get_content(), $activity_b->get_content());
                        }
                        else
                        {
                            return strcmp($activity_b->get_content(), $activity_a->get_content());
                        }
                    case Activity::PROPERTY_DATE :
                        if ($orderProperty->getDirection() == SORT_ASC)
                        {
                            return ($activity_a->get_date() < $activity_b->get_date()) ? - 1 : 1;
                        }
                        else
                        {
                            return ($activity_a->get_date() > $activity_b->get_date()) ? - 1 : 1;
                        }
                }

                return 1;
            }
            );
        }

        return array_splice($activities, $offset, $count);
    }

    protected function getActivityConditionForContentObject(ContentObject $contentObject): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Activity::class, Activity::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObject->getId())
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveActivitiesForContentObject(ContentObject $contentObject): ArrayCollection
    {
        return $this->dataClassRepository->retrieves(
            Activity::class,
            new DataClassRetrievesParameters($this->getActivityConditionForContentObject($contentObject))
        );
    }
}