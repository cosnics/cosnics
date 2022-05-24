<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use RuntimeException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentServiceBridge implements AssignmentServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    protected $learningPathAssignmentServiceBridge;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    protected $treeNodeAttempt;

    /**
     * AssignmentServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\AssignmentServiceBridgeInterface $learningPathAssignmentServiceBridge
     */
    public function __construct(
        Interfaces\AssignmentServiceBridgeInterface $learningPathAssignmentServiceBridge
    )
    {
        $this->learningPathAssignmentServiceBridge = $learningPathAssignmentServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assignment)
        {
            throw new RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     */
    public function setTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $this->treeNodeAttempt = $treeNodeAttempt;
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentServiceBridge->countDistinctEntriesByTreeNodeAndEntityType(
            $this->treeNode, $entityType
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentServiceBridge->countDistinctLateEntriesByTreeNodeAndEntityType(
            $this->treeNode, $entityType
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countEntitiesByEntityType($entityType)
    {
        return $this->learningPathAssignmentServiceBridge->countEntitiesByEntityType($this->treeNode, $entityType);
    }

    /**
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentServiceBridge->countEntitiesWithEntriesByEntityType(
            $this->treeNode, $entityType
        );
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findEntitiesWithEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentServiceBridge->findEntitiesWithEntriesByEntityType(
            $this->treeNode, $entityType
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType)
    {
        return $this->learningPathAssignmentServiceBridge->getPluralEntityNameByType($entityType);
    }

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType)
    {
        return $this->learningPathAssignmentServiceBridge->getEntityNameByType($entityType);
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, $entityType)
    {
        return $this->learningPathAssignmentServiceBridge->getEntityTableForType(
            $application, $this->treeNode, $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTableForEntityTypeAndId(Application $application, $entityType, $entityId)
    {
        return $this->learningPathAssignmentServiceBridge->getEntryTableForEntityTypeAndId(
            $application, $this->treeNode, $entityType, $entityId
        );
    }

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType()
    {
        return $this->learningPathAssignmentServiceBridge->getCurrentEntityType($this->treeNode);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser)
    {
        return $this->learningPathAssignmentServiceBridge->getCurrentEntityIdentifier($currentUser, $this->treeNode);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser)
    {
        return $this->learningPathAssignmentServiceBridge->getAvailableEntityIdentifiersForUser(
            $currentUser, $this->treeNode
        );
    }

    /**
     *
     * @param integer $date
     *
     * @return boolean
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        return $this->learningPathAssignmentServiceBridge->isDateAfterAssignmentEndTime($date);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, $entityType, $entityId)
    {
        return $this->learningPathAssignmentServiceBridge->isUserPartOfEntity($user, $entityType, $entityId);
    }

    /**
     *
     * @return boolean
     */
    public function canEditAssignment()
    {
        return $this->learningPathAssignmentServiceBridge->canEditAssignment($this->treeNode);
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAdress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress)
    {
        return $this->learningPathAssignmentServiceBridge->createEntry(
            $this->treeNode, $this->treeNodeAttempt, $entityType, $entityId, $userId, $contentObjectId, $ipAdress
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
        return $this->learningPathAssignmentServiceBridge->deleteEntry($entry);
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countEntriesForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentServiceBridge->countEntriesForTreeNodeEntityTypeAndId(
            $this->treeNode, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentServiceBridge->countDistinctScoreForTreeNodeEntityTypeAndId(
            $this->treeNode, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getAverageScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentServiceBridge->getAverageScoreForTreeNodeEntityTypeAndId(
            $this->treeNode, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getLastScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentServiceBridge->getLastScoreForTreeNodeEntityTypeAndId(
            $this->treeNode, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->learningPathAssignmentServiceBridge->findEntryByIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->learningPathAssignmentServiceBridge->findEntriesByIdentifiers($entryIdentifiers);
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity($entityType, DataClass $entity)
    {
        return $this->learningPathAssignmentServiceBridge->renderEntityNameByEntityTypeAndEntity($entityType, $entity);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->learningPathAssignmentServiceBridge->renderEntityNameByEntityTypeAndEntityId(
            $entityType, $entityId
        );
    }

    /**
     * @param Score|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score
     */
    public function createScore(Score $score)
    {
        return $this->learningPathAssignmentServiceBridge->createScore($this->treeNode, $score);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
     *
     * @throws \Exception
     */
    public function updateScore(Score $score)
    {
        $this->learningPathAssignmentServiceBridge->updateScore($this->treeNode, $score);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentServiceBridge->findScoreByEntry($entry);
    }

    /**
     * @return Score
     */
    public function initializeScore()
    {
        return $this->learningPathAssignmentServiceBridge->initializeScore();
    }

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        return $this->learningPathAssignmentServiceBridge->findEntriesByTreeNodeEntityTypeAndIdentifiers(
            $this->treeNode, $entityType, $entityIdentifiers
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findEntries()
    {
        return $this->learningPathAssignmentServiceBridge->findEntriesByTreeNode($this->treeNode);
    }

    /**
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity($entityType, $entityIdentifier)
    {
        return $this->learningPathAssignmentServiceBridge->findLastEntryForEntity(
            $this->treeNode, $entityType, $entityIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->learningPathAssignmentServiceBridge->attachContentObjectToEntry($entry, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        $this->learningPathAssignmentServiceBridge->deleteEntryAttachment($entryAttachment);
    }

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return $this->learningPathAssignmentServiceBridge->findEntryAttachmentById($entryAttachmentId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentServiceBridge->findAttachmentsByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->learningPathAssignmentServiceBridge->isContentObjectAttachedToEntry($entry, $contentObject);
    }

}