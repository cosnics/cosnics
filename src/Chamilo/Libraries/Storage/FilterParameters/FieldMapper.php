<?php
namespace Chamilo\Libraries\Storage\FilterParameters;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Class FieldMapper
 * @package Chamilo\Libraries\Storage\FilterParameters
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class FieldMapper
{
    /**
     * @var ConditionVariable[]
     */
    protected $fieldsMapping;

    /**
     * FieldMapper constructor.
     */
    public function __construct()
    {
        $this->fieldsMapping = [];
    }

    /**
     * @param string $fieldName
     * @param string $className
     * @param string $propertyName
     */
    public function addFieldMapping(string $fieldName, string $className, string $propertyName)
    {
        $this->fieldsMapping[$fieldName] = new PropertyConditionVariable($className, $propertyName);

        return $this;
    }

    /**
     * @param string $fieldName
     *
     * @return ConditionVariable
     */
    public function getConditionVariableForField(string $fieldName)
    {
        if(!array_key_exists($fieldName, $this->fieldsMapping))
        {
            throw new \InvalidArgumentException(sprintf('Field %s not found in field mapping', $fieldName));
        }

        return $this->fieldsMapping[$fieldName];
    }

    /**
     * @return array|ConditionVariable[]
     */
    public function getFieldsMapping()
    {
        return $this->fieldsMapping;
    }

}
