<?php
namespace Chamilo\Core\Metadata\Value;

/**
 * This interface determines the methods needed to create the values of elements and attributes in a context
 * Interface ValueCreator
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ValueCreator
{

    /**
     * Returns a new instance of the context implementation of the ElementValue object
     * 
     * @return ElementValue
     */
    public function create_element_value_object();

    /**
     * Returns a new instance of the context implementation of the AttributeValue object
     * 
     * @return AttributeValue
     */
    public function create_attribute_value_object();
}