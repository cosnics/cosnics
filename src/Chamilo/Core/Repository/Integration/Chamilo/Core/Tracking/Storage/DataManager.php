<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage;

use ArrayIterator;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'tracking_repository_';

    /**
     * Counts the activities for a specific content object without keeping track of the activities of his children
     *
     * @param ContentObject $contentObject
     *
     * @return integer
     * @throws \ReflectionException
     */
    public static function countDirectActivitiesForContentObject(ContentObject $contentObject)
    {
        $activity_parameters = self::get_activity_parameters(DataClassCountParameters::class, $contentObject);

        return self::count(Activity::class, $activity_parameters);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $current_content_object
     * @param $condition
     *
     * @return integer
     * @throws \ReflectionException
     */
    public static function count_activities(ContentObject $current_content_object, $condition)
    {
        $content_object_activity_count = 0;

        if ($current_content_object instanceof ComplexContentObjectSupport)
        {
            $complex_content_object_path = $current_content_object->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $content_object_activity_count += self::countDirectActivitiesForContentObject(
                    $node->get_content_object()
                );
            }
        }
        else
        {
            $content_object_activity_count += self::countDirectActivitiesForContentObject($current_content_object);
        }

        return $content_object_activity_count;
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity[] $content_object_activities
     * @param $offset
     * @param $count
     * @param \Chamilo\Libraries\Storage\Query\OrderProperty $order_property
     *
     * @return \ArrayIterator
     */
    public static function filterActivities($content_object_activities, $offset, $count, OrderProperty $order_property
    ): ArrayIterator
    {
        usort(
            $content_object_activities, function (Activity $activity_a, Activity $activity_b) use ($order_property) {
            switch ($order_property->getConditionVariable()->getPropertyName())
            {
                case Activity::PROPERTY_TYPE :
                    if ($order_property->getDirection() == SORT_ASC)
                    {
                        return strcmp($activity_a->get_type_string(), $activity_b->get_type_string());
                    }
                    else
                    {
                        return strcmp($activity_b->get_type_string(), $activity_a->get_type_string());
                    }
                    break;
                case Activity::PROPERTY_CONTENT :
                    if ($order_property->getDirection() == SORT_ASC)
                    {
                        return strcmp($activity_a->get_content(), $activity_b->get_content());
                    }
                    else
                    {
                        return strcmp($activity_b->get_content(), $activity_a->get_content());
                    }
                    break;
                case Activity::PROPERTY_DATE :
                    if ($order_property->getDirection() == SORT_ASC)
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

        $content_object_activities = array_splice($content_object_activities, $offset, $count);

        return new ArrayIterator($content_object_activities);
    }

    public static function get_activity_parameters($type, $content_object)
    {
        $activity_condition = new EqualityCondition(
            new PropertyConditionVariable(Activity::class, Activity::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($content_object->get_id())
        );

        return new $type($activity_condition);
    }

    /**
     * Retrieves the activities for a specific content object without keeping track of the activities of his children
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity>
     * @throws \Exception
     */
    public static function retrieveDirectActivitiesForContentObject(ContentObject $contentObject)
    {
        $activity_parameters = self::get_activity_parameters(DataClassRetrievesParameters::class, $contentObject);

        return self::retrieves(Activity::class, $activity_parameters);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $current_content_object
     * @param $condition
     * @param $offset
     * @param $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \ArrayIterator
     * @throws \Exception
     */
    public static function retrieve_activities(
        ContentObject $current_content_object, $condition, $offset, $count, $order_property = null
    )
    {
        $content_object_activities = [];

        if ($current_content_object instanceof ComplexContentObjectSupport)
        {
            $complex_content_object_path = $current_content_object->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $activities = self::retrieveDirectActivitiesForContentObject($node->get_content_object());

                foreach ($activities as $activity)
                {
                    $activity_instance = clone $activity;
                    $path = $node->get_fully_qualified_name(false, true);

                    if ($path)
                    {
                        $activity_instance->set_content(
                            $node->get_fully_qualified_name(false, true) . ' > ' . $activity_instance->get_content()
                        );
                    }

                    $content_object_activities[] = $activity_instance;
                }
            }
        }
        else
        {
            $activities = self::retrieveDirectActivitiesForContentObject($current_content_object);

            foreach ($activities as $activity)
            {
                $content_object_activities[] = $activity;
            }
        }

        $order_property = $order_property->getFirst();

        return self::filterActivities($content_object_activities, $offset, $count, $order_property);
    }
}