<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements \Doctrine\Common\Collections\Collection<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface>
 * @template-implements \Doctrine\Common\Collections\Selectable<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface>
 * @psalm-consistent-constructor
 */
class ConditionTranslatorRegistry
{
    public function __construct(protected ArrayCollection $conditionTranslators = new ArrayCollection())
    {
    }

    public function addConditionTranslator(ConditionTranslatorInterface $conditionTranslator): void
    {
        $this->conditionTranslators->set(get_class($conditionTranslator), $conditionTranslator);
    }

    /**
     * @template tGetTranslator
     * @param class-string<tGetTranslator> $conditionTranslatorClassName
     *
     * @return tGetTranslator|ConditionTranslatorInterface
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getTranslator(string $conditionTranslatorClassName): ConditionTranslatorInterface
    {
        if (!$this->hasConditionTranslator($conditionTranslatorClassName)) {
            throw new NoSuchClassException(
                $conditionTranslatorClassName, ConditionTranslatorInterface::class
            );
        }

        return $this->conditionTranslators->get($conditionTranslatorClassName);
    }

    public function hasConditionTranslator(string $conditionTranslatorClass): bool
    {
        return $this->conditionTranslators->containsKey($conditionTranslatorClass);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function translate(
        DBALQueryBuilder|ORMQueryBuilder $querybuilder, ConditionInterface $condition, ?bool $enableAliasing = true
    )
    {
        /** @noinspection PhpParamsInspection */
        return $this->getTranslator($condition->getConditionTranslatorClass())->translate(
            $querybuilder, $condition, $enableAliasing
        );
    }
}