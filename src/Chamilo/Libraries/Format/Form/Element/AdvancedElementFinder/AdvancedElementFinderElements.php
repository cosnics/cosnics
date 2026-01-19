<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

use Exception;

/**
 * Class to determine the elements for an advanced element finder
 *
 * @package Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder
 * @author Sven Vanpoucke
 */
class AdvancedElementFinderElements
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[]
     */
    private $elements;

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[] $elements
     */
    public function __construct($elements = [])
    {
        $this->set_elements($elements);
    }

    /**
     * @throws \Exception
     */
    public function add_element(AdvancedElementFinderElement $element = null): void
    {
        if (!$element instanceof AdvancedElementFinderElement)
        {
            throw new Exception('The element should be of type AdvancedElementFinderElement');
        }

        $this->elements[] = $element;
    }

    /**
     * Renders the elements as an array
     *
     * @return string[][]
     */
    public function as_array()
    {
        $array = [];

        $elements = $this->get_elements();

        foreach ($elements as $element)
        {
            $array[] = $element->as_array();
        }

        return $array;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[]
     */
    public function get_elements()
    {
        return $this->elements;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[] $elements
     */
    public function set_elements($elements)
    {
        $this->elements = $elements;
    }
}
