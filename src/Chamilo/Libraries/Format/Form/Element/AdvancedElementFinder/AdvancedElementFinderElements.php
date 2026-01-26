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
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[]
     */
    private array $elements;

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->setElements($elements);
    }

    /**
     * @throws \Exception
     */
    public function addElement(AdvancedElementFinderElement $element = null): void
    {
        if (!$element instanceof AdvancedElementFinderElement)
        {
            throw new Exception('The element should be of type AdvancedElementFinderElement');
        }

        $this->elements[] = $element;
    }

    /**
     * @return string[][]
     */
    public function asArray(): array
    {
        $array = [];

        $elements = $this->getElements();

        foreach ($elements as $element)
        {
            $array[] = $element->asArray();
        }

        return $array;
    }

    /**
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[] $elements
     */
    public function setElements(array $elements): static
    {
        $this->elements = $elements;

        return $this;
    }
}
