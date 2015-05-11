<?php
namespace Chamilo\Core\MetadataOld\Schema\Storage;

use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';
    
    /*
     * Retrieves a metadata schema by a given namespace @param string $namespace @return Schema
     */
    public static function retrieve_schema_by_namespace($namespace)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_NAMESPACE), 
            new StaticConditionVariable($namespace));
        
        $schema = self :: retrieve(Schema :: class_name(), $condition);
        
        if (! $schema)
        {
            throw new \InvalidArgumentException('The given namespace ' . $namespace . ' is invalid');
        }
        
        return $schema;
    }
}