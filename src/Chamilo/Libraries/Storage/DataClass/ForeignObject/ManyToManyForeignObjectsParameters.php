<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
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

    /**
     * The class name of the relation object
     *
     * @var string
     */
    private $relation_class;

    /**
     * The property that describes the base object in the relation class
     *
     * @var integer
     */
    private $base_key;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $baseObject
     * @param string $foreignClass
     * @param string $relationClass
     * @param integer $baseKey
     * @param integer $foreignKey
     *
     * @throws \ReflectionException
     */
    public function __construct($baseObject, $foreignClass, $relationClass, $baseKey = null, $foreignKey = null)
    {
        parent::__construct($baseObject, $foreignClass, $foreignKey);
        $this->set_relation_class($relationClass);
        $this->set_base_key($baseKey);
    }

    /**
     * Returns the foreign key
     *
     * @return integer
     */
    public function get_base_key()
    {
        return $this->base_key;
    }

    /**
     * Sets the foreign key If no foreign key is given, the foreign key is generated using the base class table name
     *
     * @param integer $baseKey
     *
     * @throws \ReflectionException
     */
    public function set_base_key($baseKey)
    {
        if (is_null($baseKey))
        {
            $baseKey = $this->generate_key($this->get_base_object()->getTableName());
        }

        $this->base_key = $baseKey;
    }

    /**
     * Returns the condition for the foreign objects retrieval
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_condition()
    {
        $relation_class = $this->get_relation_class();

        return new EqualityCondition(
            new PropertyConditionVariable($relation_class, $this->get_base_key()),
            new StaticConditionVariable($this->get_base_object()->getDefaultProperty(DataClass::PROPERTY_ID))
        );
    }

    /**
     * Returns the class name of the foreign object
     *
     * @return string
     */
    public function get_relation_class()
    {
        return $this->relation_class;
    }

    /**
     * Sets the class name of the foreign object
     *
     * @param string $relationClass
     */
    public function set_relation_class($relationClass)
    {
        $this->relation_class = $relationClass;
    }
}
