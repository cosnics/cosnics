<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Service to manage the repository publication functionality to check and provide publication information about
 * one or multiple given content objects in the learning paths
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathPublicationService
{
    /**
     * @var LearningPathChildService
     */
    protected $learningPathChildService;

    /**
     * @var LearningPathTreeBuilder
     */
    protected $learningPathTreeBuilder;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var LearningPathTree[]
     */
    protected $learningPathTreeCache;

    /**
     * LearningPathPublicationService constructor.
     *
     * @param LearningPathChildService $learningPathChildService
     * @param LearningPathTreeBuilder $learningPathTreeBuilder
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        LearningPathChildService $learningPathChildService, LearningPathTreeBuilder $learningPathTreeBuilder,
        ContentObjectRepository $contentObjectRepository
    )
    {
        $this->learningPathChildService = $learningPathChildService;
        $this->learningPathTreeBuilder = $learningPathTreeBuilder;
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * Checks whether or not one of the given content objects (identified by their id) is published in at
     * least one learning path
     *
     * @param array $contentObjectIds
     *
     * @return bool
     */
    public function areContentObjectsPublished($contentObjectIds = array())
    {
        return count($this->learningPathChildService->getLearningPathChildrenByContentObjects($contentObjectIds)) > 0;
    }

    /**
     * Deletes the learning path children (and their child nodes) by a given content object id in each learning path
     *
     * @param int $contentObjectId
     */
    public function deleteContentObjectPublicationsByObjectId($contentObjectId)
    {
        $learningPathChildren =
            $this->learningPathChildService->getLearningPathChildrenByContentObjects(array($contentObjectId));

        foreach ($learningPathChildren as $learningPathChild)
        {
            $learningPathTree = $this->getLearningPathTreeForLearningPathChild($learningPathChild);
            foreach ($learningPathTree->getLearningPathTreeNodes() as $learningPathTreeNode)
            {
                if ($learningPathTreeNode->getContentObject()->getId() != $contentObjectId)
                {
                    continue;
                }

                try
                {
                    $this->learningPathChildService->deleteContentObjectFromLearningPath($learningPathTreeNode);
                }
                catch (\Exception $ex)
                {
                }
            }
        }
    }

    /**
     * Deletes the learning path child (and their child nodes) by a given learning path child id
     *
     * @param int $learningPathChildId
     */
    public function deleteContentObjectPublicationsByLearningPathChildId($learningPathChildId)
    {
        $learningPathTreeNode = $this->getLearningPathTreeNodeByLearningPathChildId($learningPathChildId);
        $this->learningPathChildService->deleteContentObjectFromLearningPath($learningPathTreeNode);
    }

    /**
     * Updates the content object id in the given learning path child (identified by id)
     *
     * @param int $learningPathChildId
     * @param int $newContentObjectId
     */
    public function updateContentObjectIdInLearningPathChild($learningPathChildId, $newContentObjectId)
    {
        $learningPathTreeNode = $this->getLearningPathTreeNodeByLearningPathChildId($learningPathChildId);

        $newContentObject = new ContentObject();
        $newContentObject->setId($newContentObjectId);

        $this->learningPathChildService->updateContentObjectInLearningPathChild(
            $learningPathTreeNode, $newContentObject
        );
    }

    /**
     * Returns the ContentObject publication attributes for a given learning path child (identified by id)
     *
     * @param int $learningPathChildId
     *
     * @return Attributes
     */
    public function getContentObjectPublicationAttributesForLearningPathChild($learningPathChildId)
    {
        $learningPathChild = $this->learningPathChildService->getLearningPathChildById($learningPathChildId);

        return $this->getAttributesForLearningPathChild($learningPathChild);
    }

    /**
     * Returns the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     * @param Condition $condition
     * @param int $count
     * @param int $offset
     * @param OrderBy[] $order_properties
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForContentObject(
        $contentObjectId, $condition = null, $count = null, $offset = null, $order_properties = null
    )
    {
        $learningPathChildren =
            $this->learningPathChildService->getLearningPathChildrenByContentObjects(array($contentObjectId));

        $attributes = array();

        foreach ($learningPathChildren as $learningPathChild)
        {
            $attributes[] = $this->getAttributesForLearningPathChild($learningPathChild);
        }

        return $attributes;
    }

    /**
     * Returns the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     * @param Condition $condition
     * @param int $count
     * @param int $offset
     * @param OrderBy[] $order_properties
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForUser(
        $userId, $condition = null, $count = null, $offset = null, $order_properties = null
    )
    {
    }

    /**
     * Counts the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     * @param Condition $condition
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForContentObject($contentObjectId, $condition = null)
    {
        return count($this->learningPathChildService->getLearningPathChildrenByContentObjects(array($contentObjectId)));
    }

    /**
     * Counts the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     * @param Condition $condition
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForUser($userId, $condition = null)
    {
    }

    /**
     * Returns a learning path tree node by a given learning path child identifier
     *
     * @param int $learningPathChildId
     *
     * @return LearningPathTreeNode
     */
    protected function getLearningPathTreeNodeByLearningPathChildId($learningPathChildId)
    {
        $learningPathChild = $this->learningPathChildService->getLearningPathChildById($learningPathChildId);

        $learningPathTree = $this->getLearningPathTreeForLearningPathChild($learningPathChild);
        $learningPathTreeNode = $learningPathTree->getLearningPathTreeNodeById((int) $learningPathChildId);

        return $learningPathTreeNode;
    }

    /**
     * Builds the learning path tree that belongs to a given learning path child
     *
     * @param LearningPathChild $learningPathChild
     *
     * @return LearningPathTree
     */
    protected function getLearningPathTreeForLearningPathChild(LearningPathChild $learningPathChild)
    {
        if (!array_key_exists($learningPathChild->getLearningPathId(), $this->learningPathTreeCache))
        {
            $learningPath = $this->getLearningPathByLearningPathChild($learningPathChild);

            $this->learningPathTreeCache[$learningPathChild->getLearningPathId()] =
                $this->learningPathTreeBuilder->buildLearningPathTree($learningPath);
        }

        return $this->learningPathTreeCache[$learningPathChild->getLearningPathId()];
    }

    /**
     * Returns the learning path for the given learning path child
     *
     * @param LearningPathChild $learningPathChild
     *
     * @return LearningPath
     */
    protected function getLearningPathByLearningPathChild(LearningPathChild $learningPathChild)
    {
        $learningPath = $this->contentObjectRepository->findById($learningPathChild->getLearningPathId());

        if (!$learningPath instanceof LearningPath)
        {
            throw new \RuntimeException(
                sprintf(
                    'The given learning path child with id %s is found in a learning path that doesn\'t exist',
                    $learningPathChild->getId()
                )
            );
        }

        return $learningPath;
    }

    /**
     * Builds the publication attributes for the given learning path child
     *
     * @param LearningPathChild $learningPathChild
     *
     * @return Attributes
     */
    protected function getAttributesForLearningPathChild(LearningPathChild $learningPathChild)
    {
        $learningPath = $this->getLearningPathByLearningPathChild($learningPathChild);
        $contentObject = $this->contentObjectRepository->findById($learningPathChild->getContentObjectId());

        $attributes = new Attributes();
        $attributes->setId($learningPathChild->getId());
        $attributes->set_application('Chamilo\Core\Repository\ContentObject\LearningPath');
        $attributes->set_publisher_id($learningPath->get_owner_id());
        $attributes->set_date($contentObject->get_creation_date());
        $attributes->set_location($learningPath->get_title());
        $attributes->set_url(null);
        $attributes->set_title($contentObject->get_title());
        $attributes->set_content_object_id($learningPathChild->getContentObjectId());

        return $attributes;
    }

}