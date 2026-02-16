<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

use Chamilo\Libraries\Architecture\Exception\ClassNotExistException;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @template-implements \Doctrine\Common\Collections\Collection<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface>
 * @template-implements \Doctrine\Common\Collections\Selectable<TKey,\Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface>
 * @psalm-consistent-constructor
 */
class ConditionTranslatorCollection extends ArrayCollection
{
    public function addConditionTranslator(ConditionTranslatorInterface $conditionTranslator): void
    {
        $this->set(get_class($conditionTranslator), $conditionTranslator);
    }

    /**
     * @return string[]
     */
    public function getConditionTranslatorTypes(): array
    {
        return $this->getKeys();
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface[]
     */
    public function getConditionTranslators(): array
    {
        return $this->toArray();
    }

    /**
     * @template tGetTranslator
     * @param class-string<tGetTranslator> $conditionTranslatorClassName
     *
     * @return tGetTranslator|ConditionTranslatorInterface
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function getTranslator(string $conditionTranslatorClassName): ConditionTranslatorInterface
    {
        if (!$this->hasConditionTranslator($conditionTranslatorClassName)) {
            throw new ClassNotExistException($conditionTranslatorClassName);
        }

        return $this->get($conditionTranslatorClassName);
    }

    public function hasConditionTranslator(string $conditionTranslatorClass): bool
    {
        return $this->containsKey($conditionTranslatorClass);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function translate(QueryBuilder $querybuilder, ConditionInterface $condition, ?bool $enableAliasing = true)
    {
        /** @noinspection PhpParamsInspection */
        return $this->getTranslator($condition->getConditionTranslatorClass())->translate(
            $querybuilder, $condition, $enableAliasing
        );
    }
}