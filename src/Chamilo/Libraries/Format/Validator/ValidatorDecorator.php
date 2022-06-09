<?php
namespace Chamilo\Libraries\Format\Validator;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception;
use Symfony\Component\Validator\Mapping\MetadataInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValidatorDecorator implements ValidatorInterface
{

    protected ValidatorInterface $symfonyValidator;

    protected TranslatorInterface $translator;

    public function __construct(ValidatorInterface $symfonyValidator, TranslatorInterface $translator)
    {
        $this->symfonyValidator = $symfonyValidator;
        $this->translator = $translator;
    }

    /**
     * @throws Exception\NoSuchMetadataException
     */
    public function getMetadataFor($value): MetadataInterface
    {
        return $this->symfonyValidator->getMetadataFor($value);
    }

    /**
     * Returns whether the class is able to return metadata for the given value
     */
    public function hasMetadataFor($value): bool
    {
        return $this->symfonyValidator->hasMetadataFor($value);
    }

    /**
     * Returns a validator in the given execution context.
     *
     * The returned validator adds all generated violations to the given
     * context.
     */
    public function inContext(ExecutionContextInterface $context): ContextualValidatorInterface
    {
        return $this->symfonyValidator->inContext($context);
    }

    public function startContext()
    {
        return $this->symfonyValidator->startContext();
    }

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

    public function validateProperty(object $object, string $propertyName, $groups = null)
    {
        return $this->symfonyValidator->validateProperty($object, $propertyName, $groups);
    }

    public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null)
    {
        return $this->symfonyValidator->validatePropertyValue($objectOrClass, $propertyName, $value, $groups);
    }
}