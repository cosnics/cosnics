<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) with lazy loading.
 *
 * @package Chamilo\Libraries\Storage\DataClass\ForeignObject
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ForeignObjectsParameters
{

    private DataClass $baseObject;

    private string $foreignClass;

    private string $foreignKey;

    public function __construct(DataClass $baseObject, string $foreignClass, ?string $foreignKey = null)
    {
        $this->setBaseObject($baseObject);
        $this->setForeignClass($foreignClass);
        $this->setForeignKey($foreignKey);
    }

    protected function generateKey(string $className): string
    {
        return $className::getTableName() . '_' . DataClass::PROPERTY_ID;
    }

    public function getBaseObject(): DataClass
    {
        return $this->baseObject;
    }

    public function setBaseObject(DataClass $baseObject)
    {
        $this->baseObject = $baseObject;
    }

    public function getCondition(): Condition
    {
        return new EqualityCondition(
            new PropertyConditionVariable($this->getForeignClass(), DataClass::PROPERTY_ID),
            new StaticConditionVariable($this->getBaseObject()->getDefaultProperty($this->getForeignKey()))
        );
    }

    public function getForeignClass(): string
    {
        return $this->foreignClass;
    }

    public function setForeignClass(string $foreignClass)
    {
        $this->foreignClass = $foreignClass;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function setForeignKey(?string $foreignKey)
    {
        if (is_null($foreignKey))
        {
            $foreignKey = $this->generateKey($this->getForeignClass());
        }

        $this->foreignKey = $foreignKey;
    }
}
