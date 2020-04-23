<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
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

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataClass\ForeignObject\ForeignObjectsParameters::get_condition()
     */
    public function get_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable($this->get_foreign_class(), $this->get_foreign_key()),
            new StaticConditionVariable($this->get_base_object()->getDefaultProperty(DataClass::PROPERTY_ID))
        );
    }

    /**
     * Sets the foreign key property
     *
     * @param string $foreignKey
     *
     * @throws \ReflectionException
     */
    public function set_foreign_key($foreignKey)
    {
        if (is_null($foreignKey))
        {
            $foreignKey = $this->generate_key($this->get_base_object()->get_table_name());
        }

        parent::set_foreign_key($foreignKey);
    }
}
