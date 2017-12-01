<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
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
class LearningPathAssignmentService
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository
     */
    protected $assignmentRepository;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository $assignmentRepository
     */
    public function __construct(LearningPathAssignmentRepository $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
    }

    /**
     *
     * @param integer $treeNodeDataIdentifier
     *
     * @return integer
     */
    public function countEntriesForTreeNodeDataIdentifier($treeNodeDataIdentifier)
    {
        return $this->assignmentRepository->countEntriesForTreeNodeDataIdentifier($treeNodeDataIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeDataAndEntityType(TreeNodeData $treeNodeData, $entityType)
    {
        return $this->assignmentRepository->countDistinctEntriesByTreeNodeDataAndEntityType(
            $treeNodeData,
            $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeDataAndEntityType(TreeNodeData $treeNodeData, $entityType)
    {
        return $this->assignmentRepository->countDistinctFeedbackByTreeNodeDataAndEntityType(
            $treeNodeData,
            $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return int
     */
    public function countDistinctLateEntriesByTreeNodeDataAndEntityType(
        Assignment $assignment, TreeNodeData $treeNodeData, $entityType
    )
    {
        return $this->assignmentRepository->countDistinctLateEntriesByTreeNodeDataAndEntityType(
            $assignment, $treeNodeData, $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return integer
     */
    public function countEntitiesByTreeNodeDataAndEntityType(TreeNodeData $treeNodeData)
    {
        return $this->countTargetUsersForTreeNodeData($treeNodeData);
    }

    /**
     *
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
        TreeNodeData $treeNodeData, $userIds = [], $condition = null, $offset = null, $count = null,
        $orderProperty = null
    )
    {
        return $this->assignmentRepository->findTargetUsersForTreeNodeData(
            $treeNodeData,
            $userIds,
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param int[] $userIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersForTreeNodeData(
        TreeNodeData $treeNodeData, $userIds = [], $condition = null
    )
    {
        return $this->findTargetUsersForTreeNodeData($treeNodeData, $userIds, $condition)->count();
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
        TreeNodeData $treeNodeData,
        $entityType, $entityId
    )
    {
        return $this->assignmentRepository->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countEntriesForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType,
        $entityId
    )
    {
        return $this->assignmentRepository->countEntriesForTreeNodeDataEntityTypeAndId(
            $treeNodeData,
            $entityType,
            $entityId,
            null
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType,
        $entityId
    )
    {
        return $this->assignmentRepository->countDistinctFeedbackForEntityTypeAndId(
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId(TreeNodeData $treeNodeData, $entityType, $entityId)
    {
        return $this->assignmentRepository->countDistinctScoreForEntityTypeAndId(
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function getAverageScoreForEntityTypeAndId(TreeNodeData $treeNodeData, $entityType, $entityId)
    {
        return $this->assignmentRepository->retrieveAverageScoreForEntityTypeAndId(
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     *
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
        TreeNodeData $treeNodeData, $entityType,
        $entityId, $condition, $offset, $count, $orderProperty
    )
    {
        return $this->assignmentRepository->retrieveEntriesForTreeNodeDataEntityTypeAndId(
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
     *
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->assignmentRepository->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->assignmentRepository->retrieveEntryByIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->assignmentRepository->retrieveEntriesByIdentifiers($entryIdentifiers);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->assignmentRepository->retrieveScoreByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function findNoteByEntry(Entry $entry)
    {
        return $this->assignmentRepository->retrieveNoteByEntry($entry);
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->assignmentRepository->retrieveFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
    )
    {
        return $this->assignmentRepository->countFeedbackByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
    )
    {
        return $this->assignmentRepository->findFeedbackByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
        TreeNodeData $treeNodeData, $entityType,
        $entityIdentifiers
    )
    {
        return $this->assignmentRepository->findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
            $treeNodeData,
            $entityType,
            $entityIdentifiers
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeData(TreeNodeData $treeNodeData)
    {
        return $this->assignmentRepository->findEntriesByTreeNodeData($treeNodeData);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAddress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function createEntry(
        TreeNodeData $treeNodeData, $entityType, $entityId, $userId, $contentObjectId, $ipAddress
    )
    {
        $entry = $this->createEntryInstance();

        $entry->setTreeNodeDataId($treeNodeData->getId());
        $entry->setContentObjectId($contentObjectId);
        $entry->setSubmitted(time());
        $entry->setEntityId($entityId);
        $entry->setEntityType($entityType);
        $entry->setUserId($userId);
        $entry->setIpAddress($ipAddress);

        if(!$this->assignmentRepository->createEntry($entry))
        {
            throw new \RuntimeException('Could not create a new score for entry ' . $entry->getId());
        }

        return $entry;

    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedScore
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score
     */
    public function createScore(Entry $entry, User $user, $submittedScore)
    {
        $score = $this->createScoreInstance();

        $score->setScore($submittedScore);
        $score->setEntryId($entry->getId());
        $score->setCreated(time());
        $score->setModified(time());
        $score->setUserId($user->getId());

        if(!$this->assignmentRepository->createScore($score))
        {
            throw new \RuntimeException('Could not create a new score for entry ' . $entry->getId());
        }

        return $score;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     */
    public function updateScore(Score $score)
    {
        if(!$this->assignmentRepository->updateScore($score))
        {
            throw new \RuntimeException('Could not update the score ' . $score->getId());
        }
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedNote
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note
     */
    public function createNote(Entry $entry, User $user, $submittedNote)
    {
        $note = $this->createNoteInstance();

        $note->setNote($submittedNote);
        $note->setEntryId($entry->getId());
        $note->setCreated(time());
        $note->setModified(time());
        $note->setUserId($user->getId());

        if(!$this->assignmentRepository->createNote($note))
        {
            throw new \RuntimeException('Could not create a new note for entry ' . $note->getId());
        }

        return $note;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     */
    public function updateNote(Note $note)
    {
        if(!$this->assignmentRepository->updateNote($note))
        {
            throw new \RuntimeException('Could not update the note ' . $note->getId());
        }
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return $this->createFeedbackInstance();
    }

    /**
     * Creates a new instance for an entry
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry
     */
    protected function createEntryInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry();
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score
     */
    protected function createScoreInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score();
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback
     */
    protected function createFeedbackInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback();
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note
     */
    protected function createNoteInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note();
    }
}