<?php

namespace Chamilo\Core\Home\Rights\Storage\Repository;

use Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity;
use Chamilo\Core\Home\Rights\Storage\DataManager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Database repository for the rights entities
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsRepository
{
    /**
     * Clears the target entities for a given element
     *
     * @param Element $element
     *
     * @return bool
     */
    public function clearTargetEntitiesForElement(Element $element)
    {
        return DataManager::deletes(
            ElementTargetEntity::class_name(), $this->getElementTargetEntityConditionByElement($element)
        );
    }

    /**
     * Finds the target entities for a given element
     *
     * @param Element $element
     *
     * @return ElementTargetEntity[]
     */
    public function findTargetEntitiesForElement(Element $element)
    {
        return DataManager::retrieves(
            ElementTargetEntity::class_name(), new DataClassRetrievesParameters(
                $this->getElementTargetEntityConditionByElement(
                    $element
                )
            )
        )->as_array();
    }

    /**
     * Returns a condition for the ElementTargetEntity data class by a given element
     *
     * @param Element $element
     *
     * @return EqualityCondition
     */
    protected function getElementTargetEntityConditionByElement(Element $element)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ElementTargetEntity::class_name(), ElementTargetEntity::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->getId())
        );
    }
}