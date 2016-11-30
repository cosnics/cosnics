<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

/**
 * Class to determine the types for an advanced element finder
 * 
 * @author Sven Vanpoucke
 */
class AdvancedElementFinderElementTypes
{

    private $types;

    public function __construct($types = array())
    {
        $this->set_types($types);
    }
    
    // Setters and getters
    public function set_types($types)
    {
        $this->types = $types;
    }

    public function get_types()
    {
        return $this->types;
    }
    
    // Helper functions
    
    /**
     * Adds an element type to the types list
     * 
     * @param AdvancedElementFinderElementType $type
     */
    public function add_element_type(AdvancedElementFinderElementType $type)
    {
        if (! $type instanceof AdvancedElementFinderElementType)
        {
            return false;
        }
        
        $this->types[] = $type;
    }

    /**
     * Renders the types as an array
     * 
     * @return Array
     */
    public function as_array()
    {
        $array = array();
        
        $types = $this->get_types();
        
        foreach ($types as $type)
        {
            $array[] = $type->as_array();
        }
        
        return $array;
    }
}
