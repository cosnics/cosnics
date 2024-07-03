<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationProcessor\EntryNotificationJobProcessor;
use Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry\EntryTable;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentEphorusSupportInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\StorageParameters;
use Exception;
use RuntimeException;

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
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication
     */
    protected $assignmentPublication;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var bool
     */
    protected $canEditAssignment;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager
     */
    protected $entityServiceManager;

    /**
     * @var bool
     */
    protected $ephorusEnabled;

    /**
     * @var JobProducer
     */
    protected $jobProducer;

    /**
     * AssignmentDataProvider constructor.
     *
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     * @param \Chamilo\Core\Queue\Service\JobProducer $jobProducer
     */
    public function __construct(
        AssignmentService $assignmentService, EntityServiceManager $entityServiceManager, JobProducer $jobProducer
    )
    {
        $this->assignmentService = $assignmentService;
        $this->entityServiceManager = $entityServiceManager;
        $this->jobProducer = $jobProducer;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->assignmentService->attachContentObjectToEntry($entry, $contentObject);
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
        return $this->assignmentService->countAssignmentEntriesWithEphorusRequestsByContentObjectPublication(
            $this->contentObjectPublication, $condition
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
        return $this->assignmentService->countDistinctEntriesByContentObjectPublicationAndEntityType(
            $this->contentObjectPublication, $entityType
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
        return $this->assignmentService->countDistinctFeedbackByContentObjectPublicationAndEntityType(
            $this->contentObjectPublication, $entityType
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
        return $this->assignmentService->countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
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
        return $this->assignmentService->countDistinctLateEntriesByContentObjectPublicationAndEntityType(
            $this->getAssignment(), $this->contentObjectPublication, $entityType
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
        return $this->assignmentService->countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->countEntities($this->contentObjectPublication);
    }

    /**
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->countEntitiesWithEntries($this->contentObjectPublication);
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
        return $this->assignmentService->countEntriesForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
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
        return $this->assignmentService->countFeedbackForContentObjectPublicationByEntityTypeAndEntityId(
            $this->contentObjectPublication, $entityType, $entityId
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
        return $this->assignmentService->countFeedbackByEntry($entry);
    }

    /**
     *
     * @param int $entryIdentifier
     *
     * @return int
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->assignmentService->countFeedbackByEntryIdentifier($entryIdentifier);
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
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress)
    {
        $entry = $this->assignmentService->createEntry(
            $this->contentObjectPublication, $entityType, $entityId, $userId, $contentObjectId, $ipAdress
        );

        if ($entry instanceof Entry)
        {
            $job = new Job();
            $job->setProcessorClass(EntryNotificationJobProcessor::class)->setParameter(
                EntryNotificationJobProcessor::PARAM_ENTRY_ID, $entry->getId()
            );

            $this->jobProducer->produceJob($job, 'notifications');
        }

        return $entry;
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
            $this->assignmentService->createNote(
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
        return $this->assignmentService->createScore(
            $score
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
        $this->assignmentService->deleteEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        $this->assignmentService->deleteEntryAttachment($entryAttachment);
    }

    /**
     * @param \Chamilo\Libraries\Storage\StorageParameters $storageParameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAssignmentEntriesWithEphorusRequests(
        StorageParameters $storageParameters = new StorageParameters()
    )
    {
        return $this->assignmentService->findAssignmentEntriesWithEphorusRequestsByContentObjectPublication(
            $this->contentObjectPublication, $storageParameters
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->assignmentService->findAttachmentsByEntry($entry);
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function findEntitiesWithEntriesByEntityType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->retrieveEntitiesWithEntries($this->contentObjectPublication);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntries()
    {
        return $this->assignmentService->findEntriesByContentObjectPublication($this->contentObjectPublication);
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
        return $this->assignmentService->findEntriesByContentObjectPublicationEntityTypeAndIdentifiers(
            $this->contentObjectPublication, $entityType, $entityIdentifiers
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
        return $this->assignmentService->findEntriesByIdentifiers($entryIdentifiers);
    }

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return $this->assignmentService->findEntryAttachmentById($entryAttachmentId);
    }

    /**
     *
     * @param int $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->assignmentService->findEntryByIdentifier($entryIdentifier);
    }

    /**
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntries(array $entryIds = [])
    {
        return $this->assignmentService->findEphorusRequestsForAssignmentEntriesByContentObjectPublication(
            $this->contentObjectPublication, $entryIds
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
        return $this->assignmentService->findFeedbackByEntry($entry);
    }

    /**
     *
     * @param int $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->assignmentService->findFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity($entityType, $entityIdentifier)
    {
        return $this->assignmentService->findLastEntryForEntityByContentObjectPublication(
            $this->contentObjectPublication, $entityType, $entityIdentifier
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
        return $this->assignmentService->findNoteByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->assignmentService->findScoreByEntry($entry);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject | Assignment
     */
    protected function getAssignment()
    {
        return $this->contentObjectPublication->getContentObject();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->getAvailableEntityIdentifiersForUser($this->contentObjectPublication, $currentUser);
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
        return $this->assignmentService->getAverageScoreForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->getCurrentEntityIdentifier($this->contentObjectPublication, $currentUser);
    }

    /**
     *
     * @return int
     */
    public function getCurrentEntityType()
    {
        return $this->assignmentPublication->getEntityType();
    }

    /**
     * @param $entityType
     *
     * @return string
     */
    public function getEntityNameByType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->getEntityName();
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->getEntityTable($application, $this, $this->contentObjectPublication);
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
            $application, $this, $entityId, $entityType, $this->assignmentService, $this->contentObjectPublication
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
        return $this->assignmentService->getLastScoreForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->getPluralEntityName();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return $this->assignmentService->initializeFeedback();
    }

    /**
     * @inheritdoc
     */
    public function initializeScore()
    {
        return $this->assignmentService->initializeScore();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->assignmentService->isContentObjectAttachedToEntry($entry, $contentObject);
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, $entityType, $entityId)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->isUserPartOfEntity($user, $this->contentObjectPublication, $entityId);
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity($entityType, DataClass $entity)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->renderEntityName($entity);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->renderEntityNameById($entityId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication $assignmentPublication
     */
    public function setAssignmentPublication(Publication $assignmentPublication)
    {
        if (!isset($this->contentObjectPublication) ||
            $this->contentObjectPublication->getId() != $assignmentPublication->getPublicationId())
        {
            throw new RuntimeException(
                'The given assignment publication does not belong to the given content object publication'
            );
        }

        $this->assignmentPublication = $assignmentPublication;
    }

    /**
     * @param bool $canEditAssignment
     */
    public function setCanEditAssignment($canEditAssignment = true)
    {
        $this->canEditAssignment = $canEditAssignment;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        if (!$contentObjectPublication->getContentObject() instanceof Assignment)
        {
            throw new RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param bool $ephorusEnabled
     */
    public function setEphorusEnabled($ephorusEnabled = true)
    {
        $this->ephorusEnabled = $ephorusEnabled;
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
            $this->assignmentService->updateNote($note);
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
     */
    public function updateScore(Score $score)
    {
        $this->assignmentService->updateScore($score);
    }
}