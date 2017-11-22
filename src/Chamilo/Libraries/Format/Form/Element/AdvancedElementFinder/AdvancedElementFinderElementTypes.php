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
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinderElementType[]
     */
    private $types;

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinderElementType[] $types
     */
    public function __construct($types = array())
    {
        $this->set_types($types);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinderElementType[] $types
     */
    public function set_types($types)
    {
        $this->types = $types;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinderElementType[]
     */
    public function get_types()
    {
        return $this->types;
    }

    /**
     * Adds an element type to the types list
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinderElementType $type
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
     * @return string[][]
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
