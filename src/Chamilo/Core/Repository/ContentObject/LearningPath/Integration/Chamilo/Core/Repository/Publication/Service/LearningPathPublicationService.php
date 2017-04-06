<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
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
     * LearningPathPublicationService constructor.
     *
     * @param LearningPathChildService $learningPathChildService
     * @param LearningPathTreeBuilder $learningPathTreeBuilder
     */
    public function __construct(
        LearningPathChildService $learningPathChildService, LearningPathTreeBuilder $learningPathTreeBuilder
    )
    {
        $this->learningPathChildService = $learningPathChildService;
        $this->learningPathTreeBuilder = $learningPathTreeBuilder;
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
    }

    /**
     * Deletes the learning path child (and their child nodes) by a given learning path child id
     *
     * @param int $learningPathChildId
     */
    public function deleteContentObjectPublicationsByLearningPathChildId($learningPathChildId)
    {
    }

    /**
     * Updates the content object id in the given learning path child (identified by id)
     *
     * @param int $learningPathChildId
     * @param int $newContentObjectId
     */
    public function updateContentObjectIdInLearningPathChild($learningPathChildId, $newContentObjectId)
    {
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

}