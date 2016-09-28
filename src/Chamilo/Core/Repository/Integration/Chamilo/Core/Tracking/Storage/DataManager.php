<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'tracking_repository_';

    public static function get_activity_parameters($type, $content_object)
    {
        $activity_condition = new EqualityCondition(
            new PropertyConditionVariable(Activity :: class_name(), Activity :: PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($content_object->get_id()));

        return new $type($activity_condition);
    }

    public static function count_activities(ContentObject $current_content_object, $condition)
    {
        $content_object_activity_count = 0;

        if ($current_content_object instanceof ComplexContentObjectSupport)
        {
            $complex_content_object_path = $current_content_object->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $node_content_object = $node->get_content_object();
                $activity_parameters = self :: get_activity_parameters(
                    DataClassCountParameters :: class_name(),
                    $node_content_object);

                $activitiy_count = \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager :: count(
                    Activity :: class_name(),
                    $activity_parameters);

                $content_object_activity_count += $activitiy_count;
            }
        }
        else
        {
            $activity_parameters = self :: get_activity_parameters(
                DataClassCountParameters :: class_name(),
                $current_content_object);

            $activitiy_count = \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager :: count(
                Activity :: class_name(),
                $activity_parameters);

            $content_object_activity_count += $activitiy_count;
        }

        return $content_object_activity_count;
    }

    public static function retrieve_activities(ContentObject $current_content_object, $condition, $offset, $count,
        $order_property = null)
    {
        $content_object_activities = array();

        if ($current_content_object instanceof ComplexContentObjectSupport)
        {
            $complex_content_object_path = $current_content_object->get_complex_content_object_path();

            foreach ($complex_content_object_path->get_nodes() as $node)
            {
                $node_content_object = $node->get_content_object();
                $activity_parameters = self :: get_activity_parameters(
                    DataClassRetrievesParameters :: class_name(),
                    $node_content_object);

                $activities = \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager :: retrieves(
                    Activity :: class_name(),
                    $activity_parameters);

                while ($activity = $activities->next_result())
                {
                    $activity_instance = clone $activity;
                    $path = $node->get_fully_qualified_name(false, true);

                    if ($path)
                    {
                        $activity_instance->set_content(
                            $node->get_fully_qualified_name(false, true) . ' > ' . $activity_instance->get_content());
                    }

                    $content_object_activities[] = $activity_instance;
                }
            }
        }
        else
        {
            $activity_parameters = self :: get_activity_parameters(
                DataClassRetrievesParameters :: class_name(),
                $current_content_object);

            $activities = \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager :: retrieves(
                Activity :: class_name(),
                $activity_parameters);

            while ($activity = $activities->next_result())
            {
                $content_object_activities[] = $activity;
            }
        }

        $order_property = $order_property[0];


		
        usort(
            $content_object_activities,
            function ($activity_a, $activity_b) use($order_property) {				
                switch ($order_property->get_property()->get_property())
                {
                    case Activity :: PROPERTY_TYPE :
                        if ($order_property->get_direction() == SORT_ASC)
                        {
                            return strcmp($activity_a->get_type_string(), $activity_b->get_type_string());
                        }
                        else
                        {
                            return strcmp($activity_b->get_type_string(), $activity_a->get_type_string());
                        }

                        return strcmp($activity_a->get_type_string(), $activity_b->get_type_string());
                        break;
                    case Activity :: PROPERTY_CONTENT :
                        if ($order_property->get_direction() == SORT_ASC)
                        {
                            return strcmp($activity_a->get_content(), $activity_b->get_content());
                        }
                        else
                        {
                            return strcmp($activity_b->get_content(), $activity_a->get_content());
                        }
                        break;
                    case Activity :: PROPERTY_DATE :
                        if ($order_property->get_direction() == SORT_ASC)
                        {
                            return ($activity_a->get_date() < $activity_b->get_date()) ? - 1 : 1;
                        }
                        else
                        {
                            return ($activity_a->get_date() > $activity_b->get_date()) ? - 1 : 1;
                        }
                        break;
                }
            });

        $content_object_activities = array_splice($content_object_activities, $offset, $count);

        return new ArrayResultSet($content_object_activities);
    }
}