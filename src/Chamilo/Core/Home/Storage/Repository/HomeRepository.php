<?php
namespace Chamilo\Core\Home\Storage\Repository;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderBy;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\OrderProperty;
use Chamilo\Libraries\Storage\Architecture\Domain\StorageParameters;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Home\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class HomeRepository
{
    public function __construct(protected DataClassRepository $dataClassRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createElement(Element $element): bool
    {
        return $this->dataClassRepository->create($element);
    }

    /**
     * @param string $type
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findElementsByTypeAndParentIdentifier(
        string $type, string $parentIdentifier = DataClass::EMPTY_UUID
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

        return $this->dataClassRepository->retrieves(Element::class, $parameters);
    }
}