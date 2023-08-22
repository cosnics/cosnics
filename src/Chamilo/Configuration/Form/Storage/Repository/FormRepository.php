<?php
namespace Chamilo\Configuration\Form\Storage\Repository;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\DataClass\Option;
use Chamilo\Configuration\Form\Storage\DataClass\Value;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Configuration\Form\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countDynamicFormElementOptions(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(Option::class, new DataClassCountParameters($condition));
    }

    public function countDynamicFormElementValues(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(Value::class, new DataClassCountParameters($condition));
    }

    public function countDynamicFormElements(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(Element::class, new DataClassCountParameters($condition));
    }

    public function deleteAllOptionsFromFormElement(int $dynamicFormElementIdentifier): bool
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Option::class, Option::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new StaticConditionVariable($dynamicFormElementIdentifier)
        );

        return $this->getDataClassRepository()->deletes(Option::class, $condition);
    }

    public function deleteDynamicFormElementValuesFromForm(int $dynamicFormIdentifier): bool
    {
        $subcondition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($dynamicFormIdentifier)
        );
        $subselect = new SubselectCondition(
            new PropertyConditionVariable(Value::class, Value::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID), $subcondition
        );

        return $this->getDataClassRepository()->deletes(Value::class, $subselect);
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Option>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElementOptions(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Option::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Value>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElementValues(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Value::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Value>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElementValuesForFormIdentifierAndUserIdentifier(
        string $formIdentifier, string $userIdentifier
    ): ArrayCollection
    {
        $subcondition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($formIdentifier)
        );

        $conditions = [];
        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(Value::class, Value::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID), $subcondition
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Value::class, Value::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );
        $condition = new AndCondition($conditions);

        return $this->retrieveDynamicFormElementValues($condition);
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElements(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return $this->getDataClassRepository()->retrieves(Element::class, $parameters);
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $offset
     * @param ?int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicForms(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return $this->getDataClassRepository()->retrieves(Instance::class, $parameters);
    }

    public function retrieveInstanceForContextAndName(string $context, string $name): ?Instance
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_APPLICATION),
            new StaticConditionVariable($context)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_NAME), new StaticConditionVariable($name)
        );

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(Instance::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function selectNextDynamicFormElementOptionOrder($dynamicFormElementIdentifier): int
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Option::class, Option::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new StaticConditionVariable($dynamicFormElementIdentifier)
        );

        return $this->getDataClassRepository()->retrieveNextValue(
            Option::class, Option::PROPERTY_DISPLAY_ORDER, $condition
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function selectNextDynamicFormElementOrder(int $dynamicFormIdentifier): int
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($dynamicFormIdentifier)
        );

        return $this->getDataClassRepository()->retrieveNextValue(
            Element::class, Element::PROPERTY_DISPLAY_ORDER, $condition
        );
    }
}