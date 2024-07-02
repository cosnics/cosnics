<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentEphorusSupportInterface;
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
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentDataProvider
    implements \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider,
    AssignmentEphorusSupportInterface
{
    /**
     * @var bool
     */
    protected $canEditAssignment;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var bool
     */
    protected $ephorusEnabled;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     */
    protected $learningPath;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService
     */
    protected $learningPathAssignmentService;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @var int[]
     */
    protected $targetUserIds;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    protected $treeNodeAttempt;

    /**
     * AssignmentDataProvider constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService $learningPathAssignmentService
     */
    public function __construct(
        Translator $translator,
        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService $learningPathAssignmentService
    )
    {
        $this->translator = $translator;
        $this->learningPathAssignmentService = $learningPathAssignmentService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->learningPathAssignmentService->attachContentObjectToEntry($entry, $contentObject);
    }

    /**
     *
     * @return bool
     */
    public function canEditAssignment()
    {
        return $this->canEditAssignment;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequests(Condition $condition = null)
    {
        return $this->learningPathAssignmentService->countAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $condition
        );
    }

    /**
     *
     * @param int $entityType
     *
     * @return int
     */
    public function countDistinctEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->countDistinctEntriesByTreeNodeDataAndEntityType(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType
        );
    }

    /**
     *
     * @param int $entityType
     *
     * @return int
     */
    public function countDistinctFeedbackByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->countDistinctFeedbackByTreeNodeDataAndEntityType(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param int $entityType
     *
     * @return int
     */
    public function countDistinctLateEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->countDistinctLateEntriesByTreeNodeDataAndEntityType(
            $this->getAssignment(), $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function countDistinctScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->countDistinctScoreForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->countTargetUsersForTreeNodeData(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $this->targetUserIds
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
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $this->targetUserIds
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function countEntriesForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->countEntriesForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->countFeedbackByEntry($entry);
    }

    /**
     *
     * @param int $entryIdentifier
     *
     * @return int
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->learningPathAssignmentService->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     * @param int $userId
     * @param int $contentObjectId
     * @param string $ipAdress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress)
    {
        $user = new User();
        $user->setId($userId);

        $this->learningPathTrackingService->setActiveAttemptCompleted($this->learningPath, $this->treeNode, $user);

        return $this->learningPathAssignmentService->createEntry(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $this->treeNodeAttempt, $entityType,
            $entityId, $userId, $contentObjectId, $ipAdress
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedNote
     *
     * @return bool
     */
    public function createNote(Entry $entry, User $user, $submittedNote)
    {
        try
        {
            $this->learningPathAssignmentService->createNote(
                $entry, $user, $submittedNote
            );
        }
        catch (Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     * @param Score $score
     *
     * @return Score
     */
    public function createScore(Score $score)
    {
        $score = $this->learningPathAssignmentService->createScore(
            $score
        );

        $entry = $this->findEntryByIdentifier($score->getEntryId());

        $entryUser = new User();
        $entryUser->setId($entry->getUserId());

        $this->learningPathTrackingService->changeAssessmentScore(
            $this->learningPath, $entryUser, $this->treeNode, $entry->getTreeNodeAttemptId(), $score->getScore()
        );

        return $score;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
        $this->learningPathAssignmentService->deleteEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        $this->learningPathAssignmentService->deleteEntryAttachment($entryAttachment);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $retrievesParameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequests(
        DataClassParameters $retrievesParameters = new DataClassParameters()
    )
    {
        return $this->learningPathAssignmentService->findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $retrievesParameters
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findAttachmentsByEntry($entry);
    }

    /**
     * @param int $entityType
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntitiesWithEntriesByEntityType($entityType)
    {
        return $this->learningPathAssignmentService->findTargetUsersWithEntriesForTreeNodeData(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $this->targetUserIds
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntries()
    {
        return $this->learningPathAssignmentService->findEntriesByTreeNodeData(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData()
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        return $this->learningPathAssignmentService->findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityIdentifiers
        );
    }

    /**
     *
     * @param int $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->learningPathAssignmentService->findEntriesByIdentifiers($entryIdentifiers);
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
     *
     * @param int $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->learningPathAssignmentService->findEntryByIdentifier($entryIdentifier);
    }

    /**
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntries(array $entryIds = [])
    {
        return $this->learningPathAssignmentService->findEphorusRequestsForAssignmentEntriesByTreeNodeData(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entryIds
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findFeedbackByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findFeedbackByEntry($entry);
    }

    /**
     *
     * @param int $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->learningPathAssignmentService->findFeedbackByIdentifier($feedbackIdentifier);
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
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityIdentifier
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note
     */
    public function findNoteByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findNoteByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->learningPathAssignmentService->findScoreByEntry($entry);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject | Assignment
     */
    protected function getAssignment()
    {
        return $this->treeNode->getContentObject();
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
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function getAverageScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->getAverageScoreForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
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
     *
     * @return int
     */
    public function getCurrentEntityType()
    {
        return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry::ENTITY_TYPE_USER;
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
     * @param int $entityType
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, $entityType)
    {
        return new EntityTable(
            $application, $this, $this->learningPathAssignmentService, $this->contentObjectPublication,
            $this->treeNode->getTreeNodeData(), $this->targetUserIds
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param int $entityType
     * @param int $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTableForEntityTypeAndId(Application $application, $entityType, $entityId)
    {
        return new EntryTable(
            $application, $this, $entityId, $this->learningPathAssignmentService, $this->contentObjectPublication,
            $this->treeNode->getTreeNodeData()
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function getLastScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->learningPathAssignmentService->getLastScoreForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $this->treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param int $entityType
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
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return $this->learningPathAssignmentService->initializeFeedback();
    }

    /**
     * @return Score
     */
    public function initializeScore()
    {
        return $this->learningPathAssignmentService->initializeScore();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->learningPathAssignmentService->isContentObjectAttachedToEntry($entry, $contentObject);
    }

    /**
     *
     * @param int $date
     *
     * @return bool
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isEphorusEnabled()
    {
        return $this->ephorusEnabled;
    }

    /**
     * @param bool $ephorusEnabled
     */
    public function setEphorusEnabled($ephorusEnabled = true)
    {
        $this->ephorusEnabled = $ephorusEnabled;
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
        return $entityType ==
            \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry::ENTITY_TYPE_USER &&
            $entityId == $user->getId();
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity($entityType, DataClass $entity)
    {
        if (!$entity instanceof User)
        {
            throw new InvalidArgumentException('The given entity must be of the type ' . User::class);
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
        $user = DataManager::retrieve_by_id(User::class, $entityId);
        if (!$user instanceof User)
        {
            throw new InvalidArgumentException('The given user with id ' . $entityId . ' does not exist');
        }

        return $this->renderEntityNameByEntityTypeAndEntity($entityType, $user);
    }

    /**
     * @param bool $canEditAssignment
     */
    public function setCanEditAssignment($canEditAssignment = true)
    {
        $this->canEditAssignment = $canEditAssignment;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
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
     * @param int[] $targetUserIds
     */
    public function setTargetUserIds($targetUserIds = [])
    {
        $this->targetUserIds = $targetUserIds;
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note $note
     *
     * @return bool
     */
    public function updateNote(Note $note)
    {
        try
        {
            $this->learningPathAssignmentService->updateNote($note);
        }
        catch (Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     *
     * @throws \Exception
     */
    public function updateScore(Score $score)
    {
        $this->learningPathAssignmentService->updateScore($score);

        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry */
        $entry = $this->findEntryByIdentifier($score->getEntryId());
        if (!$entry instanceof Entry)
        {
            throw new Exception('Could not find the entry for the given score');
        }

        $entryUser = new User();
        $entryUser->setId($entry->getEntityId());

        $this->learningPathTrackingService->changeAssessmentScore(
            $this->learningPath, $entryUser, $this->treeNode, $entry->getTreeNodeAttemptId(), $score->getScore()
        );
    }
}
