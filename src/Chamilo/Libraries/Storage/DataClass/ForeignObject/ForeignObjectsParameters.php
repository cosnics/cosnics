<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
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

    /**
     * The base dataclass object for which we want to retrieve the foreign objects
     *
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $base_object;

    /**
     * The classname for the foreign object
     *
     * @var string
     */
    private $foreign_class;

    /**
     * The foreign key property
     *
     * @var string
     */
    private $foreign_key;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $baseObject
     * @param string $foreignClass
     * @param string $foreignKey
     */
    public function __construct($baseObject, $foreignClass, $foreignKey = null)
    {
        $this->set_base_object($baseObject);
        $this->set_foreign_class($foreignClass);
        $this->set_foreign_key($foreignKey);
    }

    /**
     * Returns the condition for the foreign objects retrieval
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable($this->get_foreign_class(), DataClass::PROPERTY_ID),
            new StaticConditionVariable($this->get_base_object()->get_default_property($this->get_foreign_key())));
    }

    /**
     * Generates a key property with the given class name
     *
     * @param string $className
     * @return string
     */
    protected function generate_key($className)
    {
        return $className::get_table_name() . '_' . DataClass::PROPERTY_ID;
    }

    /**
     * Returns the base object
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function get_base_object()
    {
        return $this->base_object;
    }

    /**
     * Returns the classname for the foreign object
     *
     * @return string
     */
    public function get_foreign_class()
    {
        return $this->foreign_class;
    }

    /**
     * Returns the foreign key property
     *
     * @return string
     */
    public function get_foreign_key()
    {
        return $this->foreign_key;
    }

    /**
     * Sets the base object
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $baseObject
     */
    public function set_base_object(DataClass $baseObject)
    {
        $this->base_object = $baseObject;
    }

    /**
     * Sets the classname for the foreign object
     *
     * @param string $foreignClass
     */
    public function set_foreign_class($foreignClass)
    {
        $this->foreign_class = $foreignClass;
    }

    /**
     * Sets the foreign key property
     *
     * @param string $foreignKey
     */
    public function set_foreign_key($foreignKey)
    {
        if (is_null($foreignKey))
        {
            $foreignKey = $this->generate_key($this->get_foreign_class());
        }

        $this->foreign_key = $foreignKey;
    }
}
