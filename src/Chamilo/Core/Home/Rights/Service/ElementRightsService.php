<?php
namespace Chamilo\Core\Home\Rights\Service;

use Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Doctrine\Common\Collections\ArrayCollection;
use RuntimeException;

/**
 * @package Chamilo\Core\Home\Rights\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementRightsService
{

    /**
     * Caching of the valid element ids per specific user
     *
     * @var int[]
     */
    protected array $elementIdsPerUser;

    protected RightsRepository $rightsRepository;

    public function __construct(RightsRepository $rightsRepository)
    {
        $this->rightsRepository = $rightsRepository;
    }

    public function canUserViewElement(User $user, Element $element): bool
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
    protected function getElementIdsForUser(User $user): array
    {
        $userId = $user->getId();

        if (!isset($this->elementIdsPerUser[$userId]))
        {
            $elementIdsForAllUsers = $this->getRightsRepository()->findElementIdsWithNoTargetEntities();
            $elementIdsForUser = $this->getRightsRepository()->findElementIdsTargetedForUser($user);

            $this->elementIdsPerUser[$userId] = array_merge($elementIdsForAllUsers, $elementIdsForUser);
        }

        return $this->elementIdsPerUser[$userId];
    }

    public function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    /**
     * @param \Chamilo\Core\Home\Storage\DataClass\Element $element
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getTargetEntitiesForElement(Element $element): ArrayCollection
    {
        return $this->getRightsRepository()->findTargetEntitiesForElement($element);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function setTargetEntitiesForElement(Element $element, array $targetEntities = []): void
    {
        if (!$this->getRightsRepository()->clearTargetEntitiesForElement($element))
        {
            throw new RuntimeException('Failed to delete the target entities for element ' . $element->getId());
        }

        foreach ($targetEntities as $targetEntityType => $targetEntityIdentifiers)
        {
            foreach ($targetEntityIdentifiers as $targetEntityIdentifier)
            {
                $elementTargetEntity = new ElementTargetEntity();
                $elementTargetEntity->set_element_id($element->getId());
                $elementTargetEntity->set_entity_type($targetEntityType);
                $elementTargetEntity->set_entity_id($targetEntityIdentifier);

                if (!$this->getRightsRepository()->createElementTargetEntity($elementTargetEntity))
                {
                    throw new RuntimeException(
                        sprintf(
                            'Could not create a new element target entity for element %s, entity type %s and entity id %s',
                            $element->getId(), $targetEntityType, $targetEntityIdentifier
                        )
                    );
                }
            }
        }
    }
}