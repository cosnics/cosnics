<?php
namespace Chamilo\Application\Survey\Storage;

use Chamilo\Application\Survey\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Application\Survey\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Storage\Parameters\DataClassCountDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'survey_';

    public static function retrieve_survey_publications_for_user($condition = null, $offset = null, $count = null, $order_by = null)
    {
        $joins = array();
        $join = new Join(
            RightsLocation :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_ID), 
                new PropertyConditionVariable(RightsLocation :: class_name(), RightsLocation :: PROPERTY_IDENTIFIER)));
        $joins[] = $join;
        
        $join = new Join(
            RightsLocationEntityRight :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(RightsLocation :: class_name(), RightsLocation :: PROPERTY_ID), 
                new PropertyConditionVariable(
                    RightsLocationEntityRight :: class_name(), 
                    RightsLocationEntityRight :: PROPERTY_LOCATION_ID)));
        $joins[] = $join;
        
        // $condition = new AndCondition($condition,
        // new EqualityCondition(
        // new PropertyConditionVariable(Publication :: class_name(),
        // Publication :: PROPERTY_CONTENT_OBJECT_ID),
        // new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID)));
        
        $joins = new Joins($joins);
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by, $joins);
        
        return self :: retrieves(Publication :: class_name(), $parameters);
    }

    public static function count_survey_publications_for_user($condition = null)
    {
        $joins = array();
        $join = new Join(
            RightsLocation :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_ID), 
                new PropertyConditionVariable(RightsLocation :: class_name(), RightsLocation :: PROPERTY_IDENTIFIER)));
        $joins[] = $join;
        
        $join = new Join(
            RightsLocationEntityRight :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(RightsLocation :: class_name(), RightsLocation :: PROPERTY_ID), 
                new PropertyConditionVariable(
                    RightsLocationEntityRight :: class_name(), 
                    RightsLocationEntityRight :: PROPERTY_LOCATION_ID)));
        $joins[] = $join;
        
        // $condition = new AndCondition(
        // new EqualityCondition(
        // new PropertyConditionVariable(Publication :: class_name(),
        // Publication :: PROPERTY_CONTENT_OBJECT_ID),
        // new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID)));
        
        $joins = new Joins($joins);
        $parameters = new DataClassCountDistinctParameters($condition, Publication :: PROPERTY_ID, $joins);
        
        return self :: count_distinct(Publication :: class_name(), $parameters);
    }
}
