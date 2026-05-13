<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements \Doctrine\Common\Collections\Collection<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface>
 * @template-implements \Doctrine\Common\Collections\Selectable<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface>
 * @psalm-consistent-constructor
 */
class ConditionVariableTranslatorRegistry
{
    public function __construct(protected ArrayCollection $conditionVariableTranslators = new ArrayCollection())
    {
    }

    public function addConditionVariableTranslator(ConditionVariableTranslatorInterface $conditionVariableTranslator
    ): void
    {
        $this->conditionVariableTranslators->set(get_class($conditionVariableTranslator), $conditionVariableTranslator);
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

        return $this->conditionVariableTranslators->get($conditionVariableTranslatorClassName);
    }

    public function hasConditionVariableTranslator(string $conditionPartTranslatorClass): bool
    {
        return $this->conditionVariableTranslators->containsKey($conditionPartTranslatorClass);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, ConditionVariableInterface $conditionVariable,
        ?bool $enableAliasing = true
    )
    {
        /** @noinspection PhpParamsInspection */
        return $this->getTranslator($conditionVariable->getConditionVariableTranslatorClass())->translate(
            $querybuilder, $conditionVariable, $enableAliasing
        );
    }
}