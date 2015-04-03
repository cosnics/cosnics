<?php
namespace Chamilo\Core\Metadata\ControlledVocabulary\Storage;

use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     * Retrieves a controlled vocabulary by it's value
     * 
     * @param string $value
     *
     * @return ControlledVocabulary
     */
    public static function retrieve_controlled_vocabulary_by_value($value)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ControlledVocabulary :: class_name(), ControlledVocabulary :: PROPERTY_VALUE), 
            new StaticConditionVariable($value));
        
        return self :: retrieve(ControlledVocabulary :: class_name(), $condition);
    }
}