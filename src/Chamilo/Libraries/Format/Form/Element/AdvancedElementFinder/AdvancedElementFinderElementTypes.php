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
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[]
     */
    private array $types;

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[] $types
     */
    public function __construct(array $types = [])
    {
        $this->setTypes($types);
    }

    public function addElementType(AdvancedElementFinderElementType $type): static
    {
        $this->types[] = $type;

        return $this;
    }

    /**
     * @return string[][]
     */
    public function asArray(): array
    {
        $array = [];

        $types = $this->getTypes();

        foreach ($types as $type)
        {
            $array[] = $type->asArray();
        }

        return $array;
    }

    /**
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType[] $types
     */
    public function setTypes(array $types): static
    {
        $this->types = $types;

        return $this;
    }
}
