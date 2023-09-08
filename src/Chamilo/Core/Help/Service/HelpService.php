<?php
namespace Chamilo\Core\Help\Service;

use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Storage\Repository\HelpRepository;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Help\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HelpService
{
    protected HelpRepository $helpRepository;

    public function __construct(HelpRepository $helpRepository)
    {
        $this->helpRepository = $helpRepository;
    }

    public function countHelpItemsForCondition(?Condition $condition = null): int
    {
        return $this->getHelpRepository()->countHelpItemsForCondition($condition);
    }

    public function getHelpRepository(): HelpRepository
    {
        return $this->helpRepository;
    }

    public function retrieveHelpItemByIdentifier(string $identifier): ?HelpItem
    {
        return $this->getHelpRepository()->retrieveHelpItemByIdentifier($identifier);
    }

    public function retrieveHelpItemForContextIdentifierAndLanguage(
        string $context, string $identifier, string $language
    ): ?HelpItem
    {
        return $this->getHelpRepository()->retrieveHelpItemForContextIdentifierAndLanguage(
            $context, $identifier, $language
        );
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
        return $this->getHelpRepository()->retrieveHelpItems($condition, $offset, $count, $orderBy);
    }

    public function updateHelpItem(?HelpItem $helpItem): bool
    {
        return $this->getHelpRepository()->updateHelpItem($helpItem);
    }

    public function updateHelpItemFromValues(?HelpItem $helpItem, array $values): bool
    {
        $helpItem->set_url($values[HelpItem::PROPERTY_URL]);

        return $this->updateHelpItem($helpItem);
    }
}