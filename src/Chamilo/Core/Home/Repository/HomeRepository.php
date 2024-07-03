<?php
namespace Chamilo\Core\Home\Repository;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

class HomeRepository
{
    protected DataClassRepository $dataClassRepository;

    protected RegistrationConsulter $registrationConsulter;

    public function __construct(DataClassRepository $dataClassRepository, RegistrationConsulter $registrationConsulter)
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->registrationConsulter = $registrationConsulter;
    }

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

    public function countElementsByUserIdentifier(string $userIdentifier): int
    {
        $parameters = new StorageParameters(condition: $this->getElementsByUserIdentifierCondition($userIdentifier));

        return $this->getDataClassRepository()->count(Element::class, $parameters);
    }

    public function createElement(Element $element): bool
    {
        return $this->getDataClassRepository()->create($element);
    }

    public function deleteElement(Element $element): bool
    {
        return $this->getDataClassRepository()->delete($element);
    }

    /**
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     */
    public function findBlocksByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_TYPE),
            new StaticConditionVariable(Element::TYPE_BLOCK)
        );

        return $this->getDataClassRepository()->retrieves(
            Element::class, new StorageParameters(
                condition: new AndCondition($conditions)
            )
        );
    }

    /**
     * @param string[] $columnIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
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

    public function findElementByIdentifier(string $elementIdentifier): ?Element
    {
        return $this->getDataClassRepository()->retrieveById(Element::class, $elementIdentifier);
    }

    /**
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
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
     * @param string $userIdentifier
     * @param string $parentIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     */
    public function findElementsByTypeUserIdentifierAndParentIdentifier(
        string $type, string $userIdentifier, string $parentIdentifier = '0'
    ): ArrayCollection
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_TYPE), new StaticConditionVariable($type)
        );

        $conditions[] = $this->getElementsByUserIdentifierCondition($userIdentifier);

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

    /**
     * @param string $userIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     */
    public function findElementsByUserIdentifier(string $userIdentifier): ArrayCollection
    {
        $parameters = new StorageParameters(
            condition: $this->getElementsByUserIdentifierCondition($userIdentifier), orderBy: new OrderBy([
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

    public function getElementsByUserIdentifierCondition(string $userIdentifier): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function updateElement(Element $element): bool
    {
        return $this->getDataClassRepository()->update($element);
    }
}