<?php
namespace Chamilo\Core\MetadataOld\Value\Attribute\Storage;

use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     * Retrieves a DefaultAttributeValue by a given value or controlled vocabulary
     * 
     * @param int $attribute_id
     * @param string $value
     * @param int $controlled_vocabulary_id
     *
     * @return DefaultAttributeValue
     */
    public static function retrieve_default_attribute_value_by_value_or_controlled_vocabulary($attribute_id, 
        $value = null, $controlled_vocabulary_id = null)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultAttributeValue :: class_name(), 
                DefaultAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute_id));
        
        if ($value)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    DefaultAttributeValue :: class_name(), 
                    DefaultAttributeValue :: PROPERTY_VALUE), 
                new StaticConditionVariable($value));
        }
        
        if ($conditions)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    DefaultAttributeValue :: class_name(), 
                    DefaultAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID), 
                new StaticConditionVariable($controlled_vocabulary_id));
        }
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(DefaultAttributeValue :: class_name(), $condition);
    }
}