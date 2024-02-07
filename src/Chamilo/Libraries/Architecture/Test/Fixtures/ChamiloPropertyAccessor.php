<?php
namespace Chamilo\Libraries\Architecture\Test\Fixtures;

use Symfony\Component\PropertyAccess\Exception;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Implementation of the property accessor for Chamilo underscore-based getters and setters
 *
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloPropertyAccessor implements PropertyAccessorInterface
{

    private PropertyAccessorInterface $decoratedPropertyAccessor;

    public function __construct(PropertyAccessorInterface $decoratedPropertyAccessor)
    {
        $this->decoratedPropertyAccessor = $decoratedPropertyAccessor;
    }

    /**
     * Returns the value at the end of the property path of the object
     * Example:
     * use Symfony\Component\PropertyAccess\PropertyAccess;
     * $propertyAccessor = PropertyAccess::getPropertyAccessor();
     * echo $propertyAccessor->getValue($object, 'child.name);
     * // equals echo $object->getChild()->getName();
     * This method first tries to find a public getter for each property in the
     * path. The name of the getter must be the camel-cased property name
     * prefixed with "get", "is", or "has".
     * If the getter does not exist, this method tries to find a public
     * property. The value of the property is then returned.
     * If none of them are found, an exception is thrown.
     *
     * @param object|array $objectOrArray The object or array to traverse
     * @param string|PropertyPathInterface $propertyPath The property path to read
     *
     * @return mixed The value at the end of the property path
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     *
     * @throws NoSuchPropertyException
     */
    public function getValue(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): mixed
    {
        if (is_string($propertyPath))
        {
            $propertyPath = new PropertyPath($propertyPath);
        }
        elseif (!$propertyPath instanceof PropertyPathInterface)
        {
            throw new UnexpectedTypeException(
                $propertyPath, 'string or Symfony\Component\PropertyAccess\PropertyPathInterface'
            );
        }

        $element_name = $propertyPath->getElement(0);
        $method = 'get_' . $element_name;

        if (is_object($objectOrArray))
        {
            if (!method_exists($objectOrArray, $method))
            {
                return $this->decoratedPropertyAccessor->getValue($objectOrArray, $propertyPath);
            }
            else
            {
                return call_user_func([$objectOrArray, $method]);
            }
        }
        else
        {
            return $objectOrArray[$element_name];
        }
    }

    /**
     * Returns whether a property path can be read from an object graph.
     * Whenever this method returns true, {@link getValue()} is guaranteed not
     * to throw an exception when called with the same arguments.
     *
     * @param object|array $objectOrArray The object or array to check
     * @param string|PropertyPathInterface $propertyPath The property path to check
     *
     * @return bool Whether the property path can be read
     * @throws Exception\InvalidArgumentException If the property path is invalid
     */
    public function isReadable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        try
        {
            $this->getValue($objectOrArray, $propertyPath);

            return true;
        }
        catch (\Exception)
        {
            return $this->decoratedPropertyAccessor->isReadable($objectOrArray, $propertyPath);
        }
    }

    /**
     * Returns whether a value can be written at a given property path.
     * Whenever this method returns true, {@link setValue()} is guaranteed not
     * to throw an exception when called with the same arguments.
     *
     * @param object|array $objectOrArray The object or array to check
     * @param string|PropertyPathInterface $propertyPath The property path to check
     *
     * @return bool Whether the value can be set
     * @throws Exception\InvalidArgumentException If the property path is invalid
     */
    public function isWritable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        if (!is_string($propertyPath) && !$propertyPath instanceof PropertyPathInterface)
        {
            return false;
        }

        $element_name = $propertyPath->getElement(0);
        $method = 'set_' . $element_name;

        if ((!is_object($objectOrArray) || method_exists($objectOrArray, $method)))
        {
            return true;
        }

        return $this->decoratedPropertyAccessor->isWritable($objectOrArray, $propertyPath);
    }

    /**
     * Sets the value at the end of the property path of the object
     * Example:
     * use Symfony\Component\PropertyAccess\PropertyAccess;
     * $propertyAccessor = PropertyAccess::getPropertyAccessor();
     * echo $propertyAccessor->setValue($object, 'child.name', 'Fabien');
     * // equals echo $object->getChild()->setName('Fabien');
     * This method first tries to find a public setter for each property in the
     * path. The name of the setter must be the camel-cased property name
     * prefixed with "set".
     * If the setter does not exist, this method tries to find a public
     * property. The value of the property is then changed.
     * If neither is found, an exception is thrown.
     *
     * @param object|array $objectOrArray The object or array to modify
     * @param string|PropertyPathInterface $propertyPath The property path to modify
     * @param mixed $value The value to set at the end of the property path
     *
     * @throws NoSuchPropertyException If a property does not exist or is not public.
     * @throws UnexpectedTypeException If a value within the path is neither object
     *         nor array
     */
    public function setValue(object|array &$objectOrArray, string|PropertyPathInterface $propertyPath, mixed $value
    ): void
    {
        if (is_string($propertyPath))
        {
            $propertyPath = new PropertyPath($propertyPath);
        }
        elseif (!$propertyPath instanceof PropertyPathInterface)
        {
            throw new UnexpectedTypeException(
                $propertyPath, 'string or Symfony\Component\PropertyAccess\PropertyPathInterface'
            );
        }

        $element_name = $propertyPath->getElement(0);

        $method = 'set_' . $element_name;

        if (is_object($objectOrArray))
        {
            if (!method_exists($objectOrArray, $method))
            {
                $this->decoratedPropertyAccessor->setValue($objectOrArray, $propertyPath, $value);
            }
            else
            {
                call_user_func([$objectOrArray, $method], $value);
            }
        }
        else
        {
            $objectOrArray[$element_name] = $value;
        }
    }
}