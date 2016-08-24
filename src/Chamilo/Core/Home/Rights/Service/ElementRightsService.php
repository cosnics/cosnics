<?php

namespace Chamilo\Core\Home\Rights\Service;

use Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to manage the rights for the given element types
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementRightsService
{
    /**
     * @var RightsRepository
     */
    protected $rightsRepository;

    /**
     * Caching of the valid element ids per specific user
     *
     * @var int[]
     */
    protected $elementIdsPerUser;

    /**
     * BlockTypeRightsService constructor.
     *
     * @param RightsRepository $rightsRepository
     */
    public function __construct(RightsRepository $rightsRepository)
    {
        $this->rightsRepository = $rightsRepository;
    }

    /**
     * Sets the target entities for a given element
     *
     * @param Element $element
     * @param array $targetEntities
     */
    public function setTargetEntitiesForElement(Element $element, $targetEntities = array())
    {
        if (!$this->rightsRepository->clearTargetEntitiesForElement($element))
        {
            throw new \RuntimeException('Failed to delete the target entities for element ' . $element->getId());
        }

        foreach ($targetEntities as $targetEntityType => $targetEntityIdentifiers)
        {
            foreach ($targetEntityIdentifiers as $targetEntityIdentifier)
            {
                $elementTargetEntity = new ElementTargetEntity();
                $elementTargetEntity->set_element_id($element->getId());
                $elementTargetEntity->set_entity_type($targetEntityType);
                $elementTargetEntity->set_entity_id($targetEntityIdentifier);

                if (!$elementTargetEntity->create())
                {
                    throw new \RuntimeException(
                        sprintf(
                            'Could not create a new element target entity for element %s, entity type %s and entity id %s',
                            $element->getId(), $targetEntityType, $targetEntityIdentifier
                        )
                    );
                }
            }
        }
    }

    /**
     * Returns the target entities for a given element
     *
     * @param Element $element
     *
     * @return ElementTargetEntity[]
     */
    public function getTargetEntitiesForElement(Element $element)
    {
        return $this->rightsRepository->findTargetEntitiesForElement($element);
    }

    /**
     * Checks whether or not a user can view the given element
     *
     * @param User $user
     * @param Element $element
     *
     * @return bool
     */
    public function canUserViewElement(User $user, Element $element)
    {
        return in_array($element->getId(), $this->getElementIdsForUser($user));
    }

    /**
     * Returns the element id's that have been limited to
     *
     * @param User $user
     *
     * @return int[]
     */
    protected function getElementIdsForUser(User $user)
    {
        $userId = $user->getId();

        if(!isset($this->elementIdsPerUser[$userId]))
        {
            $elementIdsForAllUsers = $this->rightsRepository->findElementIdsWithNoTargetEntities();
            $elementIdsForUser = $this->rightsRepository->findElementIdsTargetedForUser($user);

            $this->elementIdsPerUser[$userId] = array_merge($elementIdsForAllUsers, $elementIdsForUser);
        }

        return $this->elementIdsPerUser[$userId];
    }

}