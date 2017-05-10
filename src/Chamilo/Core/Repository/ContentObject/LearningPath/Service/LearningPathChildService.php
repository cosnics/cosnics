<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathChildRepository;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Money\InvalidArgumentException;

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
     * Returns the LearningPathChild objects that belong to a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return LearningPathChild[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getLearningPathChildrenForLearningPath(LearningPath $learningPath)
    {
        return $this->learningPathChildRepository->findLearningPathChildrenForLearningPath($learningPath);
    }

    /**
     * Returns the LearningPathChild objects that belong to a given content object ids (not as parent)
     *
     * @param int[] $contentObjectIds
     *
     * @return LearningPathChild[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getLearningPathChildrenByContentObjects($contentObjectIds)
    {
        return $this->learningPathChildRepository->findLearningPathChildrenByContentObjects($contentObjectIds);
    }

    /**
     * Returns the LearningPathChild objects that belong to a given user
     *
     * @param int $userId
     *
     * @return LearningPathChild[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getLearningPathChildrenByUserId($userId)
    {
        if (!is_int($userId) || $userId <= 0)
        {
            throw new \InvalidArgumentException('The given user id must be a valid integer and must be bigger than 0');
        }

        return $this->learningPathChildRepository->findLearningPathChildrenByUserId($userId);
    }

    /**
     * Returns a LearningPathChild by a given identifier
     *
     * @param int $learningPathChildId
     *
     * @return LearningPathChild
     */
    public function getLearningPathChildById($learningPathChildId)
    {
        $learningPathChild = $this->learningPathChildRepository->findLearningPathChild($learningPathChildId);
        if (!$learningPathChild)
        {
            throw new \RuntimeException(
                sprintf('The given learning path child with id %s could not be found', $learningPathChildId)
            );
        }

        return $learningPathChild;
    }

    /**
     * Adds a given content object to a learning path. Validates the content object to make sure that the
     * system does not create a cycle. Uses the LearningPathTree for calculations.
     *
     * @param LearningPath $rootLearningPath
     * @param LearningPathTreeNode $currentLearningPathTreeNode
     * @param ContentObject $childContentObject
     *
     * @param User $user
     *
     * @return LearningPathChild
     */
    public function addContentObjectToLearningPath(
        LearningPath $rootLearningPath, LearningPathTreeNode $currentLearningPathTreeNode,
        ContentObject $childContentObject, User $user
    )
    {
        $parentLearningPathTreeNode = $currentLearningPathTreeNode->getContentObject() instanceof Section ||
        $currentLearningPathTreeNode->isRootNode() ?
            $currentLearningPathTreeNode : $currentLearningPathTreeNode->getParentNode();

        $learningPathChild = new LearningPathChild();

        $learningPathChild->setLearningPathId((int) $rootLearningPath->getId());
        $learningPathChild->setParentLearningPathChildId((int) $parentLearningPathTreeNode->getId());
        $learningPathChild->setContentObjectId((int) $childContentObject->getId());
        $learningPathChild->setUserId((int) $user->getId());
        $learningPathChild->setAddedDate(time());

        if (!$this->learningPathChildRepository->create($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a LearningPathChildObject for learning path %s parent %s and child %s',
                    $learningPathChild->getLearningPathId(), $learningPathChild->getParentLearningPathChildId(),
                    $learningPathChild->getContentObjectId()
                )
            );
        }

        return $learningPathChild;
    }

    /**
     * @param string $contentObjectType
     * @param LearningPath $learningPath
     * @param LearningPathTreeNode $currentLearningPathTreeNode
     * @param User $user
     * @param string $title
     * @return LearningPathChild
     */
    public function createAndAddContentObjectToLearningPath(
        $contentObjectType, LearningPath $learningPath, LearningPathTreeNode $currentLearningPathTreeNode, User $user, $title = '...'
    )
    {
        if (!class_exists($contentObjectType) || !is_subclass_of($contentObjectType, ContentObject::class_name()))
        {
            throw new InvalidArgumentException(
                sprintf('The given ContentObject type %s is not a valid content object', $contentObjectType)
            );
        }

        /** @var ContentObject $contentObject */
        $contentObject = new $contentObjectType();
        $contentObject->set_title($title);
        $contentObject->set_owner_id($user->getId());

        if (!$this->learningPathChildRepository->create($contentObject))
        {
            throw new \RuntimeException(sprintf('Could not create a new ContentObject of type %s', $contentObjectType));
        }

        return $this->addContentObjectToLearningPath(
            $learningPath, $currentLearningPathTreeNode, $contentObject, $user
        );
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
                    $learningPathChild->getLearningPathId(), $learningPathChild->getParentLearningPathChildId(),
                    $learningPathChild->getContentObjectId()
                )
            );
        }
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

        if ($learningPathChild->getParentLearningPathChildId() != $parentLearningPathTreeNode->getId())
        {
            $learningPathChild->setParentLearningPathChildId(
                (int) $parentLearningPathTreeNode->getId()
            );
        }

        if (isset($newDisplayOrder))
        {
            $learningPathChild->setDisplayOrder((int) $newDisplayOrder);
        }

        if (!$this->learningPathChildRepository->update($learningPathChild))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the LearningPathChildObject for learning path %s parent %s and child %s',
                    $learningPathChild->getLearningPathId(), $learningPathChild->getParentLearningPathChildId(),
                    $learningPathChild->getContentObjectId()
                )
            );
        }
    }

    /**
     * Toggles the blocked status of a given ContentObject identified by a given LearningPathTreeNode
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
                    $learningPathChild->getLearningPathId(), $learningPathChild->getParentLearningPathChildId(),
                    $learningPathChild->getContentObjectId()
                )
            );
        }
    }

    /**
     * Updates the title of a given ContentObject identified by a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param string $newTitle
     */
    public function updateContentObjectTitle(LearningPathTreeNode $learningPathTreeNode, $newTitle = null)
    {
        if (empty($newTitle) || !is_string($newTitle))
        {
            throw new \InvalidArgumentException('The given title should not be empty and should be a valid string');
        }

        $contentObject = $learningPathTreeNode->getContentObject();

        if (!$contentObject instanceof ContentObject)
        {
            throw new \RuntimeException(
                sprintf(
                    'The given LearningPathTreeNode with id %s does not have a valid content object attached',
                    $learningPathTreeNode->getId()
                )
            );
        }

        $contentObject->set_title($newTitle);

        if (!$this->learningPathChildRepository->update($contentObject))
        {
            throw new \RuntimeException(
                sprintf('Could not update the Contentobject with id %S', $contentObject->getId())
            );
        }
    }

    /**
     * Deletes a content object from a learning path. The relation between the learning path and the content object
     * is defined by the learning path tree node
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @todo move to learning path service
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
                    $learningPathChild->getLearningPathId(), $learningPathChild->getParentLearningPathChildId(),
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

    /**
     * Empties the given learning path by removing all the children
     *
     * @param LearningPath $learningPath
     */
    public function emptyLearningPath(LearningPath $learningPath)
    {
        if (!$this->learningPathChildRepository->deleteChildrenFromLearningPath($learningPath))
        {
            throw new \RuntimeException('Could not empty the learning path with id ' . $learningPath->getId());
        }
    }

    /**
     * Checks whether or not the given learning path is empty
     *
     * @param LearningPath $learningPath
     *
     * @return bool
     */
    public function isLearningPathEmpty(LearningPath $learningPath)
    {
        return $this->learningPathChildRepository->countLearningPathChildrenForLearningPath($learningPath) == 0;
    }
}