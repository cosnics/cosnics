<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) in a many to many relation
 *
 * @package Chamilo\Libraries\Storage\DataClass\ForeignObject
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ManyToManyForeignObjectsParameters extends ForeignObjectsParameters
{

    private int $baseKey;

    private string $relationClass;

    public function __construct(
        DataClass $baseObject, string $foreignClass, string $relationClass, ?int $baseKey = null,
        ?string $foreignKey = null
    )
    {
        parent::__construct($baseObject, $foreignClass, $foreignKey);
        $this->setRelationClass($relationClass);
        $this->setBaseKey($baseKey);
    }

    public function getBaseKey(): int
    {
        return $this->baseKey;
    }

    public function setBaseKey(?int $baseKey)
    {
        if (is_null($baseKey))
        {
            $baseKey = $this->generateKey($this->getBaseObject()::getStorageUnitName());
        }

        $this->baseKey = $baseKey;
    }

    public function getCondition(): Condition
    {
        $relation_class = $this->getRelationClass();

        return new EqualityCondition(
            new PropertyConditionVariable($relation_class, $this->getBaseKey()),
            new StaticConditionVariable($this->getBaseObject()->getDefaultProperty(DataClass::PROPERTY_ID))
        );
    }

    public function getRelationClass(): string
    {
        return $this->relationClass;
    }

    public function setRelationClass(string $relationClass)
    {
        $this->relationClass = $relationClass;
    }
}
