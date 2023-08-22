<?php
namespace Chamilo\Configuration\Form\Service;

use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\Repository\FormRepository;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Configuration\Form\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormService
{

    private FormRepository $formRepository;

    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function countDynamicFormElementOptions(?Condition $condition = null): int
    {
        return $this->getFormRepository()->countDynamicFormElementOptions($condition);
    }

    public function countDynamicFormElementValues(?Condition $condition = null): int
    {
        return $this->getFormRepository()->countDynamicFormElementValues($condition);
    }

    public function countDynamicFormElements(?Condition $condition = null): int
    {
        return $this->getFormRepository()->countDynamicFormElements($condition);
    }

    public function deleteAllOptionsFromFormElement(int $dynamicFormElementIdentifier): bool
    {

        return $this->getFormRepository()->deleteAllOptionsFromFormElement($dynamicFormElementIdentifier);
    }

    public function deleteDynamicFormElementValuesFromForm(int $dynamicFormIdentifier): bool
    {
        return $this->getFormRepository()->deleteDynamicFormElementValuesFromForm($dynamicFormIdentifier);
    }

    protected function getFormRepository(): FormRepository
    {
        return $this->formRepository;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Option>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElementOptions(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getFormRepository()->retrieveDynamicFormElementOptions($condition, $offset, $count, $orderBy);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Value>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElementValues(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getFormRepository()->retrieveDynamicFormElementValues($condition, $offset, $count, $orderBy);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Value>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElementValuesForFormIdentifierAndUserIdentifier(
        string $formIdentifier, string $userIdentifier
    ): ArrayCollection
    {
        return $this->getFormRepository()->retrieveDynamicFormElementValuesForFormIdentifierAndUserIdentifier(
            $formIdentifier, $userIdentifier
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicFormElements(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getFormRepository()->retrieveDynamicFormElements($condition, $offset, $count, $orderBy);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveDynamicForms(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getFormRepository()->retrieveDynamicForms($condition, $offset, $count, $orderBy);
    }

    public function retrieveInstanceForContextAndName(string $context, string $name): ?Instance
    {
        return $this->getFormRepository()->retrieveInstanceForContextAndName($context, $name);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function selectNextDynamicFormElementOptionOrder($dynamicFormElementIdentifier): int
    {
        return $this->getFormRepository()->selectNextDynamicFormElementOptionOrder($dynamicFormElementIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function selectNextDynamicFormElementOrder(int $dynamicFormIdentifier): int
    {
        return $this->getFormRepository()->selectNextDynamicFormElementOrder($dynamicFormIdentifier);
    }
}