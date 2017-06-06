<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeBuilder;
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
     * @var TreeBuilder
     */
    protected $treeBuilder;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var Tree[]
     */
    protected $treeCache;

    /**
     * LearningPathPublicationService constructor.
     *
     * @param LearningPathChildService $learningPathChildService
     * @param TreeBuilder $treeBuilder
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        LearningPathChildService $learningPathChildService, TreeBuilder $treeBuilder,
        ContentObjectRepository $contentObjectRepository
    )
    {
        $this->learningPathChildService = $learningPathChildService;
        $this->treeBuilder = $treeBuilder;
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
            $tree = $this->getTreeForLearningPathChild($learningPathChild);
            foreach ($tree->getTreeNodes() as $treeNode)
            {
                if ($treeNode->getContentObject()->getId() != $contentObjectId)
                {
                    continue;
                }

                try
                {
                    $this->learningPathChildService->deleteContentObjectFromLearningPath($treeNode);
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
        $treeNode = $this->getTreeNodeByLearningPathChildId($learningPathChildId);
        $this->learningPathChildService->deleteContentObjectFromLearningPath($treeNode);
    }

    /**
     * Updates the content object id in the given learning path child (identified by id)
     *
     * @param int $learningPathChildId
     * @param int $newContentObjectId
     */
    public function updateContentObjectIdInLearningPathChild($learningPathChildId, $newContentObjectId)
    {
        $treeNode = $this->getTreeNodeByLearningPathChildId($learningPathChildId);

        $newContentObject = new ContentObject();
        $newContentObject->setId($newContentObjectId);

        $this->learningPathChildService->updateContentObjectInLearningPathChild(
            $treeNode, $newContentObject
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
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForContentObject($contentObjectId)
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
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForUser($userId)
    {
        $learningPathChildren = $this->learningPathChildService->getLearningPathChildrenByUserId((int) $userId);

        $attributes = array();

        foreach ($learningPathChildren as $learningPathChild)
        {
            $attributes[] = $this->getAttributesForLearningPathChild($learningPathChild);
        }

        return $attributes;
    }

    /**
     * Counts the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForContentObject($contentObjectId)
    {
        return count($this->learningPathChildService->getLearningPathChildrenByContentObjects(array($contentObjectId)));
    }

    /**
     * Counts the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForUser($userId)
    {
        return count($this->learningPathChildService->getLearningPathChildrenByUserId((int) $userId));
    }

    /**
     * Returns a learning path tree node by a given learning path child identifier
     *
     * @param int $learningPathChildId
     *
     * @return TreeNode
     */
    protected function getTreeNodeByLearningPathChildId($learningPathChildId)
    {
        $learningPathChild = $this->learningPathChildService->getLearningPathChildById($learningPathChildId);

        $tree = $this->getTreeForLearningPathChild($learningPathChild);
        $treeNode = $tree->getTreeNodeById((int) $learningPathChildId);

        return $treeNode;
    }

    /**
     * Builds the learning path tree that belongs to a given learning path child
     *
     * @param LearningPathChild $learningPathChild
     *
     * @return Tree
     */
    protected function getTreeForLearningPathChild(LearningPathChild $learningPathChild)
    {
        if (!array_key_exists($learningPathChild->getLearningPathId(), $this->treeCache))
        {
            $learningPath = $this->getLearningPathByLearningPathChild($learningPathChild);

            $this->treeCache[$learningPathChild->getLearningPathId()] =
                $this->treeBuilder->buildTree($learningPath);
        }

        return $this->treeCache[$learningPathChild->getLearningPathId()];
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