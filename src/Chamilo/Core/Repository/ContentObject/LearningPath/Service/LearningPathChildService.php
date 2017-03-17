<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathChildRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Service class to manage LearningPathChild classes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChildService
{
    /**
     * @var LearningPathChildRepository
     */
    protected $learningPathChildRepository;

    /**
     * @var LearningPathChildValidator
     */
    protected $learningPathChildValidator;

    /**
     * LearningPathChildService constructor.
     *
     * @param LearningPathChildRepository $learningPathChildRepository
     * @param LearningPathChildValidator $learningPathChildValidator
     */
    public function __construct(
        LearningPathChildRepository $learningPathChildRepository,
        LearningPathChildValidator $learningPathChildValidator
    )
    {
        $this->learningPathChildRepository = $learningPathChildRepository;
        $this->learningPathChildValidator = $learningPathChildValidator;
    }

    /**
     * Adds a given content object to a learning path. Validates the content object to make sure that the
     * system does not create a cycle. Uses the LearningPathTree for calculations.
     *
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     * @param ContentObject $childContentObject
     *
     * @return LearningPathChild
     */
    public function addContentObjectToLearningPath(
        LearningPathTreeNode $parentLearningPathTreeNode, ContentObject $childContentObject
    )
    {
        if (!$this->learningPathChildValidator->canContentObjectBeAdded(
            $parentLearningPathTreeNode, $childContentObject
        )
        )
        {
            throw new \RuntimeException(
                'You are not allowed to add the given content object to the parent learning path'
            );
        }

        $learningPathChild = new LearningPathChild();
        $learningPathChild->setParentLearningPathId((int) $parentLearningPathTreeNode->getContentObject()->getId());
        $learningPathChild->setContentObjectId((int) $childContentObject->getId());

        if (!$this->learningPathChildRepository->create($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a LearningPathChildObject for parent %s and child %s',
                    $learningPathChild->getParentLearningPathId(), $learningPathChild->getContentObjectId()
                )
            );
        }

        return $learningPathChild;
    }

    /**
     * Updates a content object for a given learning path child. Uses the LearningPathTree.
     * Validates the content object to make sure that the system does not create a cycle.
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param ContentObject $newContentObject
     */
    public function updateContentObjectInLearningPathChild(
        LearningPathTreeNode $learningPathTreeNode, ContentObject $newContentObject
    )
    {
        if (!$this->learningPathChildValidator->canContentObjectBeAdded(
            $learningPathTreeNode->getParentNode(), $newContentObject
        )
        )
        {
            throw new \RuntimeException(
                'You are not allowed to add the given content object to the parent learning path'
            );
        }

        $learningPathChild = $learningPathTreeNode->getLearningPathChild();
        $learningPathChild->setContentObjectId((int) $newContentObject->getId());

        if (!$this->learningPathChildRepository->update($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the LearningPathChildObject for parent %s and child %s',
                    $learningPathChild->getParentLearningPathId(), $learningPathChild->getContentObjectId()
                )
            );
        }

        $this->learningPathChildRepository->updateParentLearningPathIds(
            $learningPathTreeNode->getContentObject()->getId(), $newContentObject->getId()
        );
    }

    /**
     * Moves a content object from a learning path to a different learning path. The content object and the
     * parent learning path is identified by the learning path tree
     *
     * @param LearningPathTreeNode $selectedLearningPathTreeNode
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     * @param int $newDisplayOrder
     */
    public function moveContentObjectToOtherLearningPath(
        LearningPathTreeNode $selectedLearningPathTreeNode, LearningPathTreeNode $parentLearningPathTreeNode,
        $newDisplayOrder = null
    )
    {
        if (!$this->learningPathChildValidator->canContentObjectBeAdded(
            $parentLearningPathTreeNode, $selectedLearningPathTreeNode->getContentObject()
        )
        )
        {
            throw new \RuntimeException(
                'You are not allowed to add the given content object to the parent learning path'
            );
        }

        $learningPathChild = $selectedLearningPathTreeNode->getLearningPathChild();

        if($learningPathChild->getParentLearningPathId() != $parentLearningPathTreeNode->getContentObject()->getId())
        {
            $learningPathChild->setParentLearningPathId((int) $parentLearningPathTreeNode->getContentObject()->getId());
        }

        if(isset($newDisplayOrder) && $newDisplayOrder != $learningPathChild->getDisplayOrder())
        {
            $learningPathChild->setDisplayOrder((int) $newDisplayOrder);
        }

        if (!$this->learningPathChildRepository->update($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the LearningPathChildObject for parent %s and child %s',
                    $learningPathChild->getParentLearningPathId(), $learningPathChild->getContentObjectId()
                )
            );
        }
    }

    /**
     * Toggles the blocked status of a content object. The content object is identified by the learning path tree
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    public function toggleContentObjectBlockedStatus(LearningPathTreeNode $learningPathTreeNode)
    {
        $learningPathChild = $learningPathTreeNode->getLearningPathChild();

        if (!$learningPathChild)
        {
            throw new \InvalidArgumentException(
                'The given learning path tree node does not have a valid learning path child object'
            );
        }

        $learningPathChild->setBlocked(!$learningPathChild->isBlocked());

        if (!$this->learningPathChildRepository->update($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the LearningPathChildObject for parent %s and child %s',
                    $learningPathChild->getParentLearningPathId(), $learningPathChild->getContentObjectId()
                )
            );
        }
    }

    /**
     * Deletes a content object from a learning path. The relation between the learning path and the content object
     * is defined by the learning path tree node
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     */
    public function deleteContentObjectFromLearningPath(LearningPathTreeNode $learningPathTreeNode)
    {
        $learningPathChild = $learningPathTreeNode->getLearningPathChild();

        if (!$learningPathChild)
        {
            throw new \InvalidArgumentException(
                'The given learning path tree node does not have a valid learning path child object'
            );
        }

        if (!$this->learningPathChildRepository->delete($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not delete the LearningPathChildObject for parent %s and child %s',
                    $learningPathChild->getParentLearningPathId(), $learningPathChild->getContentObjectId()
                )
            );
        }
    }
}