<?php
namespace Chamilo\Core\Metadata\Vocabulary\Storage;

use Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassCountDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Vocabulary\Storage
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public static function count_vocabulary_users($condition)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                Vocabulary :: class_name(), 
                new ComparisonCondition(
                    new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_USER_ID), 
                    ComparisonCondition :: EQUAL, 
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID))));
        
        return self :: count_distinct(
            User :: class_name(), 
            new DataClassCountDistinctParameters($condition, User :: PROPERTY_ID, $joins));
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_property
     */
    public static function retrieve_vocabulary_users($condition, $count, $offset, $order_property)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                Vocabulary :: class_name(), 
                new ComparisonCondition(
                    new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_USER_ID), 
                    ComparisonCondition :: EQUAL, 
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID))));
        
        $properties = new DataClassProperties(
            array(
                new FunctionConditionVariable(
                    FunctionConditionVariable :: DISTINCT, 
                    new PropertiesConditionVariable(User :: class_name()))));
        
        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $order_property, $joins);
        
        return self :: records(User :: class_name(), $parameters);
    }
}