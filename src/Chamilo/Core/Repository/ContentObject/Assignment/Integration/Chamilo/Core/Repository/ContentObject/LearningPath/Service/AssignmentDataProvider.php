<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\ResultSet\DataClassResultSet;

class AssignmentDataProvider
    implements \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
{

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByEntityType($entityType)
    {
        // TODO: Implement countDistinctEntriesByEntityType() method.
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByEntityType($entityType)
    {
        // TODO: Implement countDistinctFeedbackByEntityType() method.
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByEntityType($entityType)
    {
        // TODO: Implement countDistinctLateEntriesByEntityType() method.
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countEntitiesByEntityType($entityType)
    {
        // TODO: Implement countEntitiesByEntityType() method.
    }

    /**
     *
     * @param integer $entityType
     *
     * @return string
     */
    public function getEntityNameByType($entityType)
    {
        // TODO: Implement getEntityNameByType() method.
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
        // TODO: Implement getEntityTableForType() method.
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
        // TODO: Implement getEntryTableForEntityTypeAndId() method.
    }

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType()
    {
        // TODO: Implement getCurrentEntityType() method.
    }

    /**
     *
     * @param integer $date
     *
     * @return boolean
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        // TODO: Implement isDateAfterAssignmentEndTime() method.
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId)
    {
        // TODO: Implement countFeedbackByEntityTypeAndEntityId() method.
    }

    /**
     *
     * @return boolean
     */
    public function canEditAssignment()
    {
        // TODO: Implement canEditAssignment() method.
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
        // TODO: Implement createEntry() method.
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
        // TODO: Implement countEntriesForEntityTypeAndId() method.
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
        // TODO: Implement countDistinctFeedbackForEntityTypeAndId() method.
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
        // TODO: Implement countDistinctScoreForEntityTypeAndId() method.
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
        // TODO: Implement getAverageScoreForEntityTypeAndId() method.
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        // TODO: Implement countFeedbackByEntryIdentifier() method.
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        // TODO: Implement findEntryByIdentifier() method.
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        // TODO: Implement findEntriesByIdentifiers() method.
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer
     */
    public function getEntityRendererForEntityTypeAndId($entityType, $entityId)
    {
        // TODO: Implement getEntityRendererForEntityTypeAndId() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedScore
     *
     * @return boolean
     */
    public function createScore(Entry $entry, User $user, $submittedScore)
    {
        // TODO: Implement createScore() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     *
     * @return boolean
     */
    public function updateScore(Score $score)
    {
        // TODO: Implement updateScore() method.
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
        // TODO: Implement createNote() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     *
     * @return boolean
     */
    public function updateNote(Note $note)
    {
        // TODO: Implement updateNote() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        // TODO: Implement findScoreByEntry() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function findNoteByEntry(Entry $entry)
    {
        // TODO: Implement findNoteByEntry() method.
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        // TODO: Implement initializeFeedback() method.
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        // TODO: Implement findFeedbackByIdentifier() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        // TODO: Implement countFeedbackByEntry() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return DataClassResultSet
     */
    public function findFeedbackByEntry(Entry $entry)
    {
        // TODO: Implement findFeedbackByEntry() method.
    }

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        // TODO: Implement findEntriesByEntityTypeAndIdentifiers() method.
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]
     */
    public function findEntries()
    {
        // TODO: Implement findEntries() method.
    }
}