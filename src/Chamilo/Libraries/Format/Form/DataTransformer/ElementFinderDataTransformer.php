<?php
namespace Chamilo\Libraries\Format\Form\DataTransformer;

use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms the data from the element finder (serialized) to element finder elements
 *
 * @package Chamilo\Libraries\Format\Form\DataTransformer
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementFinderDataTransformer implements DataTransformerInterface
{

    /**
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($value)
    {
        if (is_null($value))
        {
            return;
        }

        if (! $value instanceof AdvancedElementFinderElements)
        {
            throw new \InvalidArgumentException(
                'The value for the element finder must be an instance of AdvancedElementFinderElements');
        }

        return json_encode($value->as_array());
    }

    /**
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($value)
    {
        $results = array();

        if (is_null($value))
        {
            return $results;
        }

        $values = json_decode($value);

        if (is_null($values))
        {
            throw new \InvalidArgumentException('The given value ' . $value . ' can not be decoded with json_decode');
        }

        foreach ($values as $value)
        {
            $split_by_underscores = explode('_', $value);

            $id = array_pop($split_by_underscores);
            $type = implode('_', $split_by_underscores);

            $results[$type][] = $id;
        }

        return $results;
    }
}