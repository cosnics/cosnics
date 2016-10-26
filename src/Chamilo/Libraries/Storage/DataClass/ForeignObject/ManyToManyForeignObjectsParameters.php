<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) in a many to many relation
 *
 * @package common\libraries @authro Sven Vanpoucke - Hogeschool Gent
 */
class ManyToManyForeignObjectsParameters extends ForeignObjectsParameters
{

    /**
     * **************************************************************************************************************
     * Properties
     * **************************************************************************************************************
     */

    /**
     * The class name of the relation object
     *
     * @var string
     */
    private $relation_class;

    /**
     * The property that describes the base object in the relation class
     *
     * @var int
     */
    private $base_key;

    /**
     * **************************************************************************************************************
     * Main Functionality
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param DataClass $base_object
     * @param string $foreign_class
     * @param string $relation_class
     * @param int $base_key
     * @param int $foreign_key
     */
    public function __construct($base_object, $foreign_class, $relation_class, $base_key = null, $foreign_key = null)
    {
        $this->set_base_object($base_object);
        $this->set_foreign_class($foreign_class);
        $this->set_relation_class($relation_class);
        $this->set_base_key($base_key);
        $this->set_foreign_key($foreign_key);
    }

    /**
     * **************************************************************************************************************
     * Inherited Functionality
     * **************************************************************************************************************
     */

    /**
     * Returns the condition for the foreign objects retrieval
     *
     * @return Condition
     */
    public function get_condition()
    {
        $relation_class = $this->get_relation_class();

        return new EqualityCondition(
            new PropertyConditionVariable($relation_class, $this->get_base_key()),
            new StaticConditionVariable($this->get_base_object()->get_default_property(DataClass :: PROPERTY_ID)));
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters
     * **************************************************************************************************************
     */

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
     * Returns the foreign key
     *
     * @return int
     */
    public function get_base_key()
    {
        return $this->base_key;
    }

    /**
     * Sets the class name of the foreign object
     *
     * @param string $relation_class
     */
    public function set_relation_class($relation_class)
    {
        $this->relation_class = $relation_class;
    }

    /**
     * Sets the foreign key If no foreign key is given, the foreign key is generated using the base class table name
     *
     * @param int $base_key
     */
    public function set_base_key($base_key)
    {
        if (is_null($base_key))
        {
            $base_key = $this->generate_key($this->get_base_object()->get_table_name());
        }

        $this->base_key = $base_key;
    }
}
