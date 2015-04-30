<?php
namespace Chamilo\Core\MetadataOld\Value\Element\Storage;

use Chamilo\Core\MetadataOld\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     * Retrieves a DefaultElementValue by a given value or controlled vocabulary
     * 
     * @param int $element_id
     * @param string $value
     * @param int $controlled_vocabulary_id
     *
     * @return DefaultElementValue
     */
    public static function retrieve_default_element_value_by_value_or_controlled_vocabulary($element_id, $value = null, 
        $controlled_vocabulary_id = null)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(), 
                DefaultElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        if ($value)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(DefaultElementValue :: class_name(), DefaultElementValue :: PROPERTY_VALUE), 
                new StaticConditionVariable($value));
        }
        
        if ($controlled_vocabulary_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    DefaultElementValue :: class_name(), 
                    DefaultElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID), 
                new StaticConditionVariable($controlled_vocabulary_id));
        }
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(DefaultElementValue :: class_name(), $condition);
    }

    /**
     * Retrieves a list of default element values for a given element
     * 
     * @param int $element_id
     *
     * @return \libraries\storage\ResultSet<DefaultElementValue>
     */
    public static function retrieve_default_element_value_for_element($element_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(), 
                DefaultElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        return self :: retrieves(DefaultElementValue :: class_name(), $condition);
    }
}