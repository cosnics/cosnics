<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements \Doctrine\Common\Collections\Collection<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface>
 * @template-implements \Doctrine\Common\Collections\Selectable<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface>
 * @psalm-consistent-constructor
 */
class ConditionVariableTranslatorCollection extends ArrayCollection
{
    public function addConditionVariableTranslator(ConditionVariableTranslatorInterface $conditionVariableTranslator
    ): void
    {
        $this->set(get_class($conditionVariableTranslator), $conditionVariableTranslator);
    }

    /**
     * @return string[]
     */
    public function getConditionVariableTranslatorTypes(): array
    {
        return $this->getKeys();
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface[]
     */
    public function getConditionVariableTranslators(): array
    {
        return $this->toArray();
    }

    /**
     * @template tGetTranslator
     * @param class-string<tGetTranslator> $conditionVariableTranslatorClassName
     *
     * @return tGetTranslator|ConditionVariableTranslatorInterface
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getTranslator(string $conditionVariableTranslatorClassName): ConditionVariableTranslatorInterface
    {
        if (!$this->hasConditionVariableTranslator($conditionVariableTranslatorClassName)) {
            throw new NoSuchClassException(
                $conditionVariableTranslatorClassName, ConditionVariableTranslatorInterface::class
            );
        }

        return $this->get($conditionVariableTranslatorClassName);
    }

    public function hasConditionVariableTranslator(string $conditionPartTranslatorClass): bool
    {
        return $this->containsKey($conditionPartTranslatorClass);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        QueryBuilder $querybuilder, ConditionVariableInterface $conditionVariable, ?bool $enableAliasing = true
    )
    {
        /** @noinspection PhpParamsInspection */
        return $this->getTranslator($conditionVariable->getConditionVariableTranslatorClass())->translate(
            $querybuilder, $conditionVariable, $enableAliasing
        );
    }
}