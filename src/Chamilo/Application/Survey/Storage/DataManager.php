<?php
namespace Chamilo\Application\Survey\Storage;

use Chamilo\Application\Survey\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Application\Survey\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

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

        $joins = new Joins($joins);
        $parameters = new DataClassCountParameters(
            $condition,
            $joins,
            new FunctionConditionVariable(
                FunctionConditionVariable :: DISTINCT,
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_ID)));

        return self :: count(Publication :: class_name(), $parameters);
    }
}
