<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentEphorusSupportInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry\EntryTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentDataProvider
    implements \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider, AssignmentEphorusSupportInterface
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathAssignmentService
     */
    protected $learningPathAssignmentService;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     */
    protected $learningPath;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    protected $treeNodeAttempt;

    /**
     * @var int[]
     */
    protected $targetUserIds;

    /**
     * @var bool
     */
    protected $canEditAssignment;

    /**
     * AssignmentDataProvider constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathAssignmentService $learningPathAssignmentService
     */
    public function __construct(Translator $translator, LearningPathAssignmentService $learningPathAssignmentService)
    {
        $this->translator = $translator;
        $this->learningPathAssignmentService = $learningPathAssignmentService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assignment)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject | Assignment
     */
    protected function getAssignment()
    {
        return $this->treeNode->getContentObject();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     */
    public function setTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $this->treeNodeAttempt = $treeNodeAttempt;
    }

    /**
     * @param int[] $targetUserIds
     */
    public function setTargetUserIds($targetUserIds = [])
    {
        $this->targetUserIds = $targetUserIds;
    }

    /**
     * @param bool $canEditAssignment
     */
    public function setCanEditAssignment($canEditAssignment = true)
    {
        $this->canEditAssignment = $canEditAssignment;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     */
    public function setLearningPath(LearningPath $learningPath)
    {
        $this->learningPath = $learningPath;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService $trackingService
     */
    public function setLearningPathTrackingService(TrackingService $trackingService)
    {
        $this->learningPathTrackingService = $trackingService;
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->countDistinctEntriesByTreeNodeDataAndEntityType(
            $this->treeNode->getTreeNodeData(), $entityType
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->countDistinctFeedbackByTreeNodeDataAndEntityType(
            $this->treeNode->getTreeNodeData(), $entityType
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
        return $this->learningPathAssignmentService->countDistinctLateEntriesByTreeNodeDataAndEntityType(
            $this->getAssignment(), $this->treeNode->getTreeNodeData(), $entityType
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
        return $this->learningPathAssignmentService->countTargetUsersForTreeNodeData(
            $this->treeNode->getTreeNodeData(), $this->targetUserIds
        );
    }

    /**
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->countTargetUsersWithEntriesForTreeNodeData(
            $this->treeNode->getTreeNodeData(), $this->targetUserIds
        );
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findEntitiesWithEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->findTargetUsersWithEntriesForTreeNodeData(
            $this->treeNode->getTreeNodeData(), $this->targetUserIds
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
        return $this->translator->trans(
            'Users', [],
            'Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath'
        );
    }

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType)
    {
        return $this->translator->trans(
            'User', [],
            'Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath'
        );
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
        return new EntityTable(
            $application, $this, $this->learningPathAssignmentService, $this->treeNode->getTreeNodeData(),
            $this->targetUserIds
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
        return new EntryTable(
            $application, $this, $entityId, $this->learningPathAssignmentService, $this->treeNode->getTreeNodeData()
        );
    }

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType()
    {
        return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::ENTITY_TYPE_USER;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser)
    {
        return $currentUser->getId();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser)
    {
        return [$currentUser->getId()];
    }

    /**
     *
     * @param integer $date
     *
     * @return boolean
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        return false;
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
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
        return $entityType == \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::ENTITY_TYPE_USER &&
            $entityId == $user->getId();
    }

    /**
     *
     * @return boolean
     */
    public function canEditAssignment()
    {
        return $this->canEditAssignment;
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAdress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress)
    {
        $user = new User();
        $user->setId($userId);

        $this->learningPathTrackingService->setActiveAttemptCompleted($this->learningPath, $this->treeNode, $user);

        return $this->learningPathAssignmentService->createEntry(
            $this->treeNode->getTreeNodeData(), $this->treeNodeAttempt, $entityType, $entityId, $userId,
            $contentObjectId, $ipAdress
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
        $this->learningPathAssignmentService->deleteEntry($entry);
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
        return $this->learningPathAssignmentService->countEntriesForTreeNodeDataEntityTypeAndId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
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
        return $this->learningPathAssignmentService->countDistinctScoreForTreeNodeDataEntityTypeAndId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
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
        return $this->learningPathAssignmentService->getAverageScoreForTreeNodeDataEntityTypeAndId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
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
        return $this->learningPathAssignmentService->getLastScoreForTreeNodeDataEntityTypeAndId(
            $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->learningPathAssignmentService->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->learningPathAssignmentService->findEntryByIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->learningPathAssignmentService->findEntriesByIdentifiers($entryIdentifiers);
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity($entityType, DataClass $entity)
    {
        if(!$entity instanceof User)
        {
            throw new \InvalidArgumentException('The given entity must be of the type ' . User::class);
        }

        return $entity->get_fullname();
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class, $entityId);
        return $this->renderEntityNameByEntityTypeAndEntity($entityType, $user);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedScore
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function createScore(Entry $entry, User $user, $submittedScore)
    {
        $score = $this->learningPathAssignmentService->createScore(
            $entry, $user, $submittedScore
        );

        $entryUser = new User();
        $entryUser->setId($entry->getEntityId());

        $this->learningPathTrackingService->changeAssessmentScore(
            $this->learningPath, $entryUser, $this->treeNode, $entry->getTreeNodeAttemptId(), $submittedScore
        );

        return $score;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     * @throws \Exception
     */
    public function updateScore(Score $score)
    {
        $this->learningPathAssignmentService->updateScore($score);

        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry $entry */
        $entry = $this->findEntryByIdentifier($score->getEntryId());
        if (!$entry instanceof Entry)
        {
            throw new \Exception('Could not find the entry for the given score');
        }

        $entryUser = new User();
        $entryUser->setId($entry->getEntityId());

        $this->learningPathTrackingService->changeAssessmentScore(
            $this->learningPath, $entryUser, $this->treeNode, $entry->getTreeNodeAttemptId(), $score->getScore()
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedNote
     *
     * @return boolean
     */
    public function createNote(Entry $entry, User $user, $submittedNote)
    {
        try
        {
            $this->learningPathAssignmentService->createNote(
                $entry, $user, $submittedNote
            );
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     *
     * @return boolean
     */
    public function updateNote(Note $note)
    {
        try
        {
            $this->learningPathAssignmentService->updateNote($note);
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findScoreByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function findNoteByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findNoteByEntry($entry);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return $this->learningPathAssignmentService->initializeFeedback();
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->learningPathAssignmentService->findFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->countFeedbackByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findFeedbackByEntry($entry);
    }

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        return $this->learningPathAssignmentService->findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
            $this->treeNode->getTreeNodeData(), $entityType, $entityIdentifiers
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntries()
    {
        return $this->learningPathAssignmentService->findEntriesByTreeNodeData($this->treeNode->getTreeNodeData());
    }

    /**
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity($entityType, $entityIdentifier)
    {
        return $this->learningPathAssignmentService->findLastEntryForEntity(
            $this->treeNode->getTreeNodeData(), $entityType, $entityIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->learningPathAssignmentService->attachContentObjectToEntry($entry, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        $this->learningPathAssignmentService->deleteEntryAttachment($entryAttachment);
    }

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return $this->learningPathAssignmentService->findEntryAttachmentById($entryAttachmentId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findAttachmentsByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->learningPathAssignmentService->isContentObjectAttachedToEntry($entry, $contentObject);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithRequests(Condition $condition = null)
    {
        return null;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findAssignmentEntriesWithRequests(RecordRetrievesParameters $recordRetrievesParameters = null)
    {
        return new RecordIterator(ContentObject::class_name(), []);
    }
}