<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

/**
 * Class to determine the types for an advanced element finder
 *
 * @package Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder
 * @author Sven Vanpoucke
 */
class AdvancedElementFinderElementTypes
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[]
     */
    private $types;

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[] $types
     */
    public function __construct($types = [])
    {
        $this->set_types($types);
    }

    /**
     * Adds an element type to the types list
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType $type
     */
    public function add_element_type(AdvancedElementFinderElementType $type)
    {
        $this->types[] = $type;
    }

    /**
     * Renders the types as an array
     *
     * @return string[][]
     */
    public function as_array()
    {
        $array = [];

        $types = $this->get_types();

        foreach ($types as $type)
        {
            $array[] = $type->as_array();
        }

        return $array;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[]
     */
    public function get_types()
    {
        return $this->types;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[] $types
     */
    public function set_types($types)
    {
        $this->types = $types;
    }
}
