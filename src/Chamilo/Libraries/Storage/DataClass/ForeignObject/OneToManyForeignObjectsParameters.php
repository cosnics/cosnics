<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) in a one to many relation
 * 
 * @package common\libraries @authro Sven Vanpoucke - Hogeschool Gent
 */
class OneToManyForeignObjectsParameters extends ForeignObjectsParameters
{

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
        return new EqualityCondition(
            new PropertyConditionVariable($this->get_foreign_class(), $this->get_foreign_key()), 
            new StaticConditionVariable($this->get_base_object()->get_default_property(DataClass::PROPERTY_ID)));
    }

    /**
     * Sets the foreign key If no foreign key is given, the foreign key is generated using the base class table name
     * 
     * @param int $foreign_key
     */
    public function set_foreign_key($foreign_key)
    {
        if (is_null($foreign_key))
        {
            $foreign_key = $this->generate_key($this->get_base_object()->get_table_name());
        }
        
        parent::set_foreign_key($foreign_key);
    }
}
