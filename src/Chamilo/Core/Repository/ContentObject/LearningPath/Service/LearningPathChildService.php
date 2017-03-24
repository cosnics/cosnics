<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
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
     * LearningPathChildService constructor.
     *
     * @param LearningPathChildRepository $learningPathChildRepository
     */
    public function __construct(LearningPathChildRepository $learningPathChildRepository)
    {
        $this->learningPathChildRepository = $learningPathChildRepository;
    }

    /**
     * Adds a given content object to a learning path. Validates the content object to make sure that the
     * system does not create a cycle. Uses the LearningPathTree for calculations.
     *
     * @param LearningPath $rootLearningPath
     * @param LearningPathTreeNode $parentLearningPathTreeNode
     * @param ContentObject $childContentObject
     *
     * @return LearningPathChild
     */
    public function addContentObjectToLearningPath(
        LearningPath $rootLearningPath, LearningPathTreeNode $parentLearningPathTreeNode,
        ContentObject $childContentObject
    )
    {
        $learningPathChild = new LearningPathChild();

        $learningPathChild->setLearningPathId((int) $rootLearningPath->getId());
        $learningPathChild->setSectionContentObjectId((int) $parentLearningPathTreeNode->getId());
        $learningPathChild->setContentObjectId((int) $childContentObject->getId());

        if (!$this->learningPathChildRepository->create($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a LearningPathChildObject for learning path %s parent %s and child %s',
                    $learningPathChild->getLearningPathId(), $learningPathChild->getSectionContentObjectId(),
                    $learningPathChild->getContentObjectId()
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
        $learningPathChild = $learningPathTreeNode->getLearningPathChild();
        $learningPathChild->setContentObjectId((int) $newContentObject->getId());

        if (!$this->learningPathChildRepository->update($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the LearningPathChildObject for learning path %s parent %s and child %s',
                    $learningPathChild->getLearningPathId(), $learningPathChild->getSectionContentObjectId(),
                    $learningPathChild->getContentObjectId()
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
        $learningPathChild = $selectedLearningPathTreeNode->getLearningPathChild();

        if ($learningPathChild->getSectionContentObjectId() != $parentLearningPathTreeNode->getId())
        {
            $learningPathChild->setSectionContentObjectId(
                (int) $parentLearningPathTreeNode->getId()
            );
        }

        if (isset($newDisplayOrder) && $newDisplayOrder != $learningPathChild->getDisplayOrder())
        {
            $learningPathChild->setDisplayOrder((int) $newDisplayOrder);
        }

        if (!$this->learningPathChildRepository->update($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the LearningPathChildObject for learning path %s parent %s and child %s',
                    $learningPathChild->getLearningPathId(), $learningPathChild->getSectionContentObjectId(),
                    $learningPathChild->getContentObjectId()
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
                    'Could not update the LearningPathChildObject for learning path %s parent %s and child %s',
                    $learningPathChild->getLearningPathId(), $learningPathChild->getSectionContentObjectId(),
                    $learningPathChild->getContentObjectId()
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
                    'Could not delete the LearningPathChildObject for learning path %s parent %s and child %s',
                    $learningPathChild->getLearningPathId(), $learningPathChild->getSectionContentObjectId(),
                    $learningPathChild->getContentObjectId()
                )
            );
        }

        $childNodes = $learningPathTreeNode->getChildNodes();
        foreach ($childNodes as $childNode)
        {
            $this->deleteContentObjectFromLearningPath($childNode);
        }
    }
}