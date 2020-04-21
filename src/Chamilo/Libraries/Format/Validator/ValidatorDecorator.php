<?php

namespace Chamilo\Libraries\Format\Validator;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorDecorator implements ValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $symfonyValidator;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * ValidatorDecorator constructor.
     *
     * @param ValidatorInterface $symfonyValidator
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(ValidatorInterface $symfonyValidator, TranslatorInterface $translator)
    {
        $this->symfonyValidator = $symfonyValidator;
        $this->translator = $translator;
    }

    /**
     * Returns the metadata for the given value.
     *
     * @param mixed $value Some value
     *
     * @return \Symfony\Component\Validator\Mapping\MetadataInterface The metadata for the value
     *
     * @throws Exception\NoSuchMetadataException If no metadata exists for the given value
     */
    public function getMetadataFor($value)
    {
        return $this->symfonyValidator->getMetadataFor($value);
    }

    /**
     * Returns whether the class is able to return metadata for the given value.
     *
     * @param mixed $value Some value
     *
     * @return bool Whether metadata can be returned for that value
     */
    public function hasMetadataFor($value)
    {
        return $this->symfonyValidator->hasMetadataFor($value);
    }

    /**
     * Returns a validator in the given execution context.
     *
     * The returned validator adds all generated violations to the given
     * context.
     *
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     *
     * @return ContextualValidatorInterface The validator for that context
     */
    public function inContext(ExecutionContextInterface $context)
    {
        return $this->symfonyValidator->inContext($context);
    }

    /**
     * Starts a new validation context and returns a validator for that context.
     *
     * The returned validator collects all violations generated within its
     * context. You can access these violations with the
     * {@link ContextualValidatorInterface::getViolations()} method.
     *
     * @return ContextualValidatorInterface The validator for the new context
     */
    public function startContext()
    {
        return $this->symfonyValidator->startContext();
    }

    /**
     * Validates a value against a constraint or a list of constraints.
     *
     * If no constraint is passed, the constraint
     * {@link \Symfony\Component\Validator\Constraints\Valid} is assumed.
     *
     * @param mixed $value The value to validate
     * @param Constraint|Constraint[] $constraints The constraint(s) to validate
     *                                             against
     * @param array|null $groups The validation groups to
     *                                             validate. If none is given,
     *                                             "Default" is assumed
     *
     * @return ConstraintViolationListInterface A list of constraint violations
     *                                          If the list is empty, validation
     *                                          succeeded
     */
    public function validate($value, $constraints = null, $groups = null)
    {
        /** @var ConstraintViolationListInterface|\Symfony\Component\Validator\ConstraintViolation[] $constraintViolationList */
        $constraintViolationList = $this->symfonyValidator->validate($value, $constraints, $groups);
        $decoratedConstraintViolationList = new ConstraintViolationList();

        foreach ($constraintViolationList as $constraintViolation)
        {
            $payload = $constraintViolation->getConstraint()->payload;
            if (!array_key_exists('context', $payload))
            {
                $decoratedConstraintViolationList->add($constraintViolation);
                continue;
            }

            $convertedParameters = [];

            $parameters = $constraintViolation->getParameters();
            array_walk(
                $parameters, function ($parameter, $key) use (&$convertedParameters) {
                $key = str_replace('{{ ', '{', $key);
                $key = str_replace(' }}', '}', $key);

                $convertedParameters[$key] = $parameter;
            }
            );

            $context = $payload['context'];
            $translatedMessage =
                $this->translator->trans($constraintViolation->getMessage(), $convertedParameters, $context);

            $constraintViolation = new ConstraintViolation(
                $translatedMessage, $constraintViolation->getMessageTemplate(), $constraintViolation->getParameters(),
                $constraintViolation->getRoot(), $constraintViolation->getPropertyPath(),
                $constraintViolation->getInvalidValue(), $constraintViolation->getPlural(),
                $constraintViolation->getCode(), $constraintViolation->getConstraint(), $constraintViolation->getCause()
            );

            $decoratedConstraintViolationList->add($constraintViolation);
        }

        return $decoratedConstraintViolationList;
    }

    /**
     * Validates a property of an object against the constraints specified
     * for this property.
     *
     * @param object $object The object
     * @param string $propertyName The name of the validated property
     * @param array|null $groups The validation groups to validate. If
     *                                 none is given, "Default" is assumed
     *
     * @return ConstraintViolationListInterface A list of constraint violations
     *                                          If the list is empty, validation
     *                                          succeeded
     */
    public function validateProperty($object, $propertyName, $groups = null)
    {
        return $this->symfonyValidator->validateProperty($object, $propertyName, $groups);
    }

    /**
     * Validates a value against the constraints specified for an object's
     * property.
     *
     * @param object|string $objectOrClass The object or its class name
     * @param string $propertyName The name of the property
     * @param mixed $value The value to validate against the
     *                                     property's constraints
     * @param array|null $groups The validation groups to validate. If
     *                                     none is given, "Default" is assumed
     *
     * @return ConstraintViolationListInterface A list of constraint violations
     *                                          If the list is empty, validation
     *                                          succeeded
     */
    public function validatePropertyValue($objectOrClass, $propertyName, $value, $groups = null)
    {
        return $this->symfonyValidator->validatePropertyValue($objectOrClass, $propertyName, $value, $groups);
    }
}