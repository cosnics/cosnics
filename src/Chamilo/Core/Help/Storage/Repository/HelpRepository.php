<?php
namespace Chamilo\Core\Help\Storage\Repository;

use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Help\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HelpRepository
{
    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countHelpItemsForCondition(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(HelpItem::class, new DataClassCountParameters($condition));
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    public function retrieveHelpItemByIdentifier(string $identifier): ?HelpItem
    {
        return $this->getDataClassRepository()->retrieveById(HelpItem::class, $identifier);
    }

    public function retrieveHelpItemForContextIdentifierAndLanguage(
        string $context, string $identifier, string $language
    ): ?HelpItem
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_CONTEXT),
            new StaticConditionVariable($context)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_IDENTIFIER),
            new StaticConditionVariable($identifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_LANGUAGE),
            new StaticConditionVariable($language)
        );

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(HelpItem::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Help\Storage\DataClass\HelpItem>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveHelpItems(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            HelpItem::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }

    public function updateHelpItem(?HelpItem $helpItem): bool
    {
        return $this->getDataClassRepository()->update($helpItem);
    }

}