<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface AssignmentDataProvider
{
    const AVERAGE_SCORE = 'average_score';
    const ENTITY_NAME = 'entity_name';

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countEntitiesByEntityType($entityType);

    /**
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType($entityType);

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function findEntitiesWithEntriesByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType);

    /**
     * @param $entityType
     *
     * @return string
     */
    public function getEntityNameByType($entityType);

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, $entityType);

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTableForEntityTypeAndId(Application $application, $entityType, $entityId);

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType();

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser);

    /**
     *
     * @param integer $date
     *
     * @return boolean
     */
    public function isDateAfterAssignmentEndTime($date);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, $entityType, $entityId);

    /**
     *
     * @return boolean
     */
    public function canEditAssignment();

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
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress);

    /**
     * @param Entry $entry
     */
    public function deleteEntry(Entry $entry);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countEntriesForEntityTypeAndId($entityType, $entityId);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId($entityType, $entityId);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getAverageScoreForEntityTypeAndId($entityType, $entityId);

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function getLastScoreForEntityTypeAndId($entityType, $entityId);

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier);

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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]
     */
    public function findEntriesByIdentifiers($entryIdentifiers);

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity($entityType, DataClass $entity);

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId);

    /**
     * @param Score $score
     * @return Score
     */
    public function createScore(Score $score);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     *
     * @return boolean
     */
    public function updateScore(Score $score);

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function initializeScore();

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedNote
     *
     * @return boolean
     */
    public function createNote(Entry $entry, User $user, $submittedNote);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note $note
     *
     * @return boolean
     */
    public function updateNote(Note $note);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note
     */
    public function findNoteByEntry(Entry $entry);

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function initializeFeedback();

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry);

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntry(Entry $entry);

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers);

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]
     */
    public function findEntries();

    /**
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity($entityType, $entityIdentifier);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment);

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject);
}