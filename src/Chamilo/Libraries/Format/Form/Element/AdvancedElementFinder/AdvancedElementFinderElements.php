<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

/**
 * Class to determine the elements for an advanced element finder
 * 
 * @author Sven Vanpoucke
 */
class AdvancedElementFinderElements
{

    private $elements;

    public function __construct($elements = array())
    {
        $this->set_elements($elements);
    }
    
    // Setters and getters
    public function set_elements($elements)
    {
        $this->elements = $elements;
    }

    public function get_elements()
    {
        return $this->elements;
    }
    
    // Helper functions
    
    /**
     * Adds an element to the elements list
     * 
     * @param AdvancedElementFinderElement $element
     */
    public function add_element(AdvancedElementFinderElement $element = null)
    {
        if (! $element instanceof AdvancedElementFinderElement)
        {
            return false;
        }
        
        $this->elements[] = $element;
    }

    /**
     * Renders the elements as an array
     * 
     * @return Array
     */
    public function as_array()
    {
        $array = array();
        
        $elements = $this->get_elements();
        
        foreach ($elements as $element)
        {
            $array[] = $element->as_array();
        }
        
        return $array;
    }
}
