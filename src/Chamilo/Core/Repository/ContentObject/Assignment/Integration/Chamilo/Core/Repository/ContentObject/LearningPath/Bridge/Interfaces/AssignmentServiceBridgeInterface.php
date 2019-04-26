<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface AssignmentServiceBridgeInterface
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AssignmentServiceBridgeInterface
{
    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeAndEntityType(TreeNode $treeNode, $entityType);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByTreeNodeAndEntityType(TreeNode $treeNode, $entityType);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return mixed
     */
    public function findEntitiesByEntityType(TreeNode $treeNode, int $entityType, FilterParameters $filterParameters);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countEntitiesByEntityType(TreeNode $treeNode, $entityType, FilterParameters $filterParameters);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType(TreeNode $treeNode, $entityType);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findEntitiesWithEntriesByEntityType(TreeNode $treeNode, $entityType);

    /**
     * @param integer $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType);

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType);

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters $entityTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, EntityTableParameters $entityTableParameters);

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters $entryTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTableForEntityTypeAndId(
        Application $application, EntryTableParameters $entryTableParameters
    );

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return integer
     */
    public function getCurrentEntityType(TreeNode $treeNode);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser, TreeNode $treeNode);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser, TreeNode $treeNode);

    /**
     * @param integer $date
     *
     * @return boolean
     */
    public function isDateAfterAssignmentEndTime($date);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(TreeNode $treeNode, User $user, $entityType, $entityId);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(TreeNode $treeNode, int $entityType, int $entityId);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return boolean
     */
    public function canEditAssignment(TreeNode $treeNode);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAdress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function createEntry(
        TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId, $userId, $contentObjectId,
        $ipAdress
    );

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntriesForTreeNodeEntityTypeAndId(
        TreeNode $treeNode, $entityType, $entityId, Condition $condition
    );

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForTreeNodeEntityTypeAndId(TreeNode $treeNode, $entityType, $entityId);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getAverageScoreForTreeNodeEntityTypeAndId(TreeNode $treeNode, $entityType, $entityId);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getLastScoreForTreeNodeEntityTypeAndId(TreeNode $treeNode, $entityType, $entityId);

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier);

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByIdentifiers($entryIdentifiers);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity(TreeNode $treeNode, $entityType, DataClass $entity);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId(TreeNode $treeNode, $entityType, $entityId);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param Score $score
     *
     * @return Score
     */
    public function createScore(TreeNode $treeNode, Score $score);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
     */
    public function updateScore(TreeNode $treeNode, Score $score);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry);

    /**
     * @return Score
     */
    public function initializeScore();

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeEntityTypeAndIdentifiers(TreeNode $treeNode, $entityType, $entityIdentifiers);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityId
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeEntityTypeAndId(
        TreeNode $treeNode, int $entityType, int $entityId, Condition $condition = null, int $offset = null,
        int $count = null,
        array $orderProperty = []
    );

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNode(TreeNode $treeNode);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity(TreeNode $treeNode, $entityType, $entityIdentifier);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment);

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject);
}