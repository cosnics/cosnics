<?php
namespace Chamilo\Core\Metadata\Schema\Storage;

use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use InvalidArgumentException;

/**
 *
 * @package Chamilo\Core\Metadata\Schema\Storage
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     * Retrieves a metadata schema by a given namespace
     * 
     * @param string $namespace
     * @return \Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema
     */
    public static function retrieveSchemaByNamespace($namespace)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Schema::class, Schema::PROPERTY_NAMESPACE),
            ComparisonCondition::EQUAL, 
            new StaticConditionVariable($namespace));
        
        $schema = self::retrieve(Schema::class, new DataClassRetrieveParameters($condition));
        
        if (! $schema)
        {
            throw new InvalidArgumentException('The given namespace ' . $namespace . ' is invalid');
        }
        
        return $schema;
    }
}