<?php
namespace Chamilo\Libraries\Format\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms the data for the DatePickerFormType
 * From integer timestamp to string and reverse
 *
 * @package Chamilo\Libraries\Format\Form\DataTransformer
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DatePickerDataTransformer implements DataTransformerInterface
{

    /**
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($value)
    {
        if (is_null($value))
        {
            return null;
        }

        if (! is_numeric($value))
        {
            throw new \InvalidArgumentException('The value for the datepicker should be a valid timestamp');
        }

        return date('d/m/Y  H:i', $value);
    }

    /**
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($value)
    {
        if (empty($value))
        {
            return null;
        }

        $timestamp = strtotime($value);

        if (! $timestamp || ! is_numeric($timestamp))
        {
            throw new \InvalidArgumentException('The given value ' . $value . ' could not be transformed to a timestamp');
        }

        return $timestamp;
    }
}