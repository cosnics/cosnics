<?php
namespace Chamilo\Core\Home\Storage\Repository;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\StorageParameters;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Home\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HomeRepository
{
    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function countElementsByParentIdentifier(string $parentIdentifier): int
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getDataClassRepository()->count(
            Element::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createElement(Element $element): bool
    {
        return $this->getDataClassRepository()->create($element);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function deleteElement(Element $element): bool
    {
        return $this->getDataClassRepository()->delete($element);
    }

    /**
     * @param string[] $columnIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findBlocksForColumnIdentifiers(array $columnIdentifiers): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_PARENT_ID), $columnIdentifiers
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_TYPE),
            new StaticConditionVariable(Element::TYPE_BLOCK)
        );

        return $this->getDataClassRepository()->retrieves(
            Element::class, new StorageParameters(condition: new AndCondition($conditions))
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findColumnIdentifiersForTabIdentifier(string $tabIdentifier): array
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_PARENT_ID), new StaticConditionVariable(
                $tabIdentifier
            )
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_TYPE),
            new StaticConditionVariable(Element::TYPE_COLUMN)
        );

        return $this->getDataClassRepository()->distinct(
            Element::class, new StorageParameters(
                condition: new AndCondition($conditions), retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID)]
            )
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->getDataClassRepository()->retrieveById(Element::class, $elementIdentifier);
    }

    /**
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findElementsByParentIdentifier(string $parentIdentifier): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        return $this->getDataClassRepository()->retrieves(
            Element::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * @param string $type
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findElementsByTypeAndParentIdentifier(
        string $type, string $parentIdentifier = '0'
    ): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_TYPE), new StaticConditionVariable($type)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parentIdentifier)
        );

        $parameters = new StorageParameters(
            condition: new AndCondition($conditions), orderBy: new OrderBy([
            new OrderProperty(new PropertyConditionVariable(Element::class, Element::PROPERTY_TYPE)),
            new OrderProperty(new PropertyConditionVariable(Element::class, Element::PROPERTY_SORT))
        ])
        );

        return $this->getDataClassRepository()->retrieves(Element::class, $parameters);
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateElement(Element $element): bool
    {
        return $this->getDataClassRepository()->update($element);
    }
}