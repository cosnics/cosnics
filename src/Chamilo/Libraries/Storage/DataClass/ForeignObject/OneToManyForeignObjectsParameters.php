<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) in a one to many relation
 *
 * @package Chamilo\Libraries\Storage\DataClass\ForeignObject
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OneToManyForeignObjectsParameters extends ForeignObjectsParameters
{

    public function getCondition(): Condition
    {
        return new EqualityCondition(
            new PropertyConditionVariable($this->getForeignClass(), $this->getForeignKey()),
            new StaticConditionVariable($this->getBaseObject()->getDefaultProperty(DataClass::PROPERTY_ID))
        );
    }

    public function setForeignKey(?string $foreignKey)
    {
        if (is_null($foreignKey))
        {
            $foreignKey = $this->generateKey($this->getBaseObject()::getStorageUnitName());
        }

        parent::setForeignKey($foreignKey);
    }
}
