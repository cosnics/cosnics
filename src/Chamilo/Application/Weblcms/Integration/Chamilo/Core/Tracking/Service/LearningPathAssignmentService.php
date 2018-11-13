<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentEphorusRepository;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationProcessor\EntryNotificationJobProcessor;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathAssignmentService extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathAssignmentService
{
    /**
     *
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository
     */
    protected $assignmentRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentEphorusRepository
     */
    protected $assignmentEphorusRepository;

    /**
     * @var \Chamilo\Core\Queue\Service\JobProducer
     */
    protected $jobProducer;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository $assignmentRepository
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentEphorusRepository $learningPathAssignmentEphorusRepository
     * @param \Chamilo\Core\Queue\Service\JobProducer $jobProducer
     */
    public function __construct(LearningPathAssignmentRepository $assignmentRepository, LearningPathAssignmentEphorusRepository $learningPathAssignmentEphorusRepository, JobProducer $jobProducer)
    {
        parent::__construct($assignmentRepository, $learningPathAssignmentEphorusRepository);
        $this->jobProducer = $jobProducer;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param integer $treeNodeDataIdentifier
     *
     * @return integer
     */
    public function countEntriesForTreeNodeDataIdentifier(ContentObjectPublication $contentObjectPublication, $treeNodeDataIdentifier)
    {
        return $this->assignmentRepository->countEntriesForTreeNodeDataIdentifier($contentObjectPublication, $treeNodeDataIdentifier);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeDataAndEntityType(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType)
    {
        return $this->assignmentRepository->countDistinctEntriesByTreeNodeDataAndEntityType(
            $contentObjectPublication,
            $treeNodeData,
            $entityType
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeDataAndEntityType(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType)
    {
        return $this->assignmentRepository->countDistinctFeedbackByTreeNodeDataAndEntityType(
            $contentObjectPublication,
            $treeNodeData,
            $entityType
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return int
     */
    public function countDistinctLateEntriesByTreeNodeDataAndEntityType(
        Assignment $assignment, ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType
    )
    {
        return $this->assignmentRepository->countDistinctLateEntriesByTreeNodeDataAndEntityType(
            $assignment, $contentObjectPublication, $treeNodeData, $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param int[] $userIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersForTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $userIds = [], $condition = null, $offset = null, $count = null,
        $orderProperty = null
    )
    {
        return $this->assignmentRepository->findTargetUsersForTreeNodeData(
            $contentObjectPublication,
            $treeNodeData,
            $userIds,
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param int[] $userIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersForTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $userIds = [], $condition = null
    )
    {
        return $this->findTargetUsersForTreeNodeData($contentObjectPublication, $treeNodeData, $userIds, $condition)->count();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param array $userIds
     *
     * @return int
     */
    public function countTargetUsersWithEntriesForTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $userIds = []
    )
    {
        return $this->findTargetUsersWithEntriesForTreeNodeData($contentObjectPublication, $treeNodeData, $userIds)->count();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $userIds
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetUsersWithEntriesForTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $userIds = [], $condition = null, $offset = null, $count = null, $orderProperty = []
    )
    {
        return $this->assignmentRepository->findTargetUsersWithEntriesForTreeNodeData(
            $contentObjectPublication, $treeNodeData, $userIds, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->assignmentRepository->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
            $contentObjectPublication, $treeNodeData, $entityType, $entityId
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countEntriesForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType,
        $entityId, Condition $condition = null
    )
    {
        return $this->assignmentRepository->countEntriesForTreeNodeDataEntityTypeAndId(
            $contentObjectPublication,
            $treeNodeData,
            $entityType,
            $entityId,
            $condition
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->assignmentRepository->countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
            $contentObjectPublication,
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForTreeNodeDataEntityTypeAndId(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId)
    {
        return $this->assignmentRepository->countDistinctScoreForTreeNodeDataEntityTypeAndId(
            $contentObjectPublication,
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function getAverageScoreForTreeNodeDataEntityTypeAndId(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId)
    {
        return $this->assignmentRepository->retrieveAverageScoreForTreeNodeDataEntityTypeAndId(
            $contentObjectPublication,
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function getLastScoreForTreeNodeDataEntityTypeAndId(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId)
    {
        return $this->assignmentRepository->retrieveLastScoreForTreeNodeDataEntityTypeAndId(
            $contentObjectPublication,
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType,
        $entityId, $condition, $offset, $count, $orderProperty
    )
    {
        return $this->assignmentRepository->retrieveEntriesForTreeNodeDataEntityTypeAndId(
            $contentObjectPublication,
            $treeNodeData,
            $entityType,
            $entityId,
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType,
        $entityIdentifiers
    )
    {
        return $this->assignmentRepository->findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
            $contentObjectPublication,
            $treeNodeData,
            $entityType,
            $entityIdentifiers
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeData(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData)
    {
        return $this->assignmentRepository->findEntriesByTreeNodeData($contentObjectPublication, $treeNodeData);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry
     */
    public function findLastEntryForEntity(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityIdentifier)
    {
        return $this->assignmentRepository->findLastEntryForEntityByTreeNodeData(
            $contentObjectPublication, $treeNodeData, $entityType, $entityIdentifier
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt|\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAddress
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createEntry(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId, $userId, $contentObjectId,
        $ipAddress
    )
    {
        /** @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry */
        $entry = $this->createEntryInstance();

        $entry->setTreeNodeDataId($treeNodeData->getId());
        $entry->setTreeNodeAttemptId($treeNodeAttempt->getId());
        $entry->setContentObjectPublicationId($contentObjectPublication->getId());

        $entry = $this->createEntryByInstance($entry, $entityType, $entityId, $userId, $contentObjectId, $ipAddress);

        if($entry instanceof Entry)
        {
            $job = new Job();
            $job->setProcessorClass(EntryNotificationJobProcessor::class)
                ->setParameter(EntryNotificationJobProcessor::PARAM_ENTRY_ID, $entry->getId())
                ->setParameter(EntryNotificationJobProcessor::PARAM_CONTENT_OBJECT_PUBLICATION_ID, $treeNodeAttempt->get_publication_id());

            $this->jobProducer->produceJob($job, 'notifications');
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequestsByTreeNodeData(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, Condition $condition = null)
    {
        return $this->assignmentEphorusRepository->countAssignmentEntriesWithRequestsByTreeNodeData($contentObjectPublication, $treeNodeData, $condition);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequestsByTreeNodeData(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, RecordRetrievesParameters $recordRetrievesParameters = null)
    {
        return $this->assignmentEphorusRepository->findAssignmentEntriesWithRequestsByTreeNodeData($contentObjectPublication, $treeNodeData, $recordRetrievesParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntriesByTreeNodeData(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $entryIds = [])
    {
        if(empty($entryIds))
        {
            return [];
        }

        return $this->assignmentEphorusRepository->findEphorusRequestsForAssignmentEntriesByTreeNodeData($contentObjectPublication, $treeNodeData, $entryIds);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     */
    public function deleteEntriesByTreeNodeData(ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData)
    {
        $entries = $this->findEntriesByTreeNodeData($contentObjectPublication, $treeNodeData);
        foreach($entries as $entry)
        {
            $this->deleteEntry($entry);
        }
    }


    /**
     * Creates a new instance for an entry
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry
     */
    protected function createEntryInstance()
    {
        return new \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry(
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\EntryAttachment
     */
    protected function createEntryAttachmentInstance()
    {
        return new \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\EntryAttachment();
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score
     */
    protected function createScoreInstance()
    {
        return new \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score(
        );
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback
     */
    protected function createFeedbackInstance()
    {
        return new \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback(
        );
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Note
     */
    protected function createNoteInstance()
    {
        return new \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Note(
        );
    }

    /**
     * @return string
     */
    public function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry::class;
    }

    /**
     * @return string
     */
    public function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score::class;
    }

    /**
     * @return string
     */
    public function getFeedbackClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback::class;
    }
}
