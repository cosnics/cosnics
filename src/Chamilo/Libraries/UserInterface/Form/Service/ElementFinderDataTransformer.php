<?php
namespace Chamilo\Libraries\UserInterface\Form\Service;

use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use InvalidArgumentException;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms the data from the element finder (serialized) to element finder elements
 *
 * @package Chamilo\Libraries\UserInterface\Form\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementFinderDataTransformer implements DataTransformerInterface
{
    public function reverseTransform($value): array
    {
        $results = [];

        if (is_null($value)) {
            return $results;
        }

        $values = json_decode($value);

        if (is_null($values)) {
            throw new InvalidArgumentException('The given value ' . $value . ' can not be decoded with json_decode');
        }

        foreach ($values as $value) {
            $split_by_underscores = explode('_', $value);

            $id = array_pop($split_by_underscores);
            $type = implode('_', $split_by_underscores);

            $results[$type][] = $id;
        }

        return $results;
    }

    public function transform($value): string|null|false
    {
        if (is_null($value)) {
            return null;
        }

        if (!$value instanceof AdvancedElementFinderElements) {
            throw new InvalidArgumentException(
                'The value for the element finder must be an instance of AdvancedElementFinderElements'
            );
        }

        return json_encode($value->asArray());
    }
}