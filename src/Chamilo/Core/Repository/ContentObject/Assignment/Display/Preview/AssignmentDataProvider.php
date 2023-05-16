<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssignmentDataProvider implements
    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
{

    public function initializeScore()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countDistinctEntriesByEntityType()
     */
    public function countDistinctEntriesByEntityType($entityType)
    {
        return 1;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countDistinctFeedbackByEntityType()
     */
    public function countDistinctFeedbackByEntityType($entityType)
    {
        return 0;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countDistinctLateEntriesByEntityType()
     */
    public function countDistinctLateEntriesByEntityType($entityType)
    {
        return 0;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countEntitiesByEntityType()
     */
    public function countEntitiesByEntityType($entityType)
    {
        return 13;
    }

    /**
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType($entityType)
    {
        return 0;
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function findEntitiesWithEntriesByEntityType($entityType)
    {
        return [];
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getPluralEntityNameByType()
     */
    public function getPluralEntityNameByType($entityType)
    {
        return Translation::get('Users');
    }

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType)
    {
        return Translation::get('User');
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getEntityTableForType()
     */
    public function getEntityTableForType(Application $application, $entityType)
    {
        return new EntityTable($application, $this);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::canEditAssignment()
     */
    public function canEditAssignment()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countDistinctFeedbackForEntityTypeAndId()
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countDistinctScoreForEntityTypeAndId()
     */
    public function countDistinctScoreForEntityTypeAndId($entityType, $entityId)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countEntriesForEntityTypeAndId()
     */
    public function countEntriesForEntityTypeAndId($entityType, $entityId)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countFeedbackByEntityTypeAndEntityId()
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId)
    {
        // TODO Auto-generated method stub
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
        return false;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countFeedbackByEntryIdentifier()
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::createEntry()
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress)
    {
        // TODO Auto-generated method stub
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findEntryByIdentifier()
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getAverageScoreForEntityTypeAndId()
     */
    public function getAverageScoreForEntityTypeAndId($entityType, $entityId)
    {
        return null;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getAverageScoreForEntityTypeAndId()
     */
    public function getLastScoreForEntityTypeAndId($entityType, $entityId)
    {
        return null;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getCurrentEntityType()
     */
    public function getCurrentEntityType()
    {
        // TODO Auto-generated method stub
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getEntryTableForEntityTypeAndId()
     */
    public function getEntryTableForEntityTypeAndId(
        Application $application,
        $entityType, $entityId
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::isDateAfterAssignmentEndTime()
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::countFeedbackByEntry()
     */
    public function countFeedbackByEntry(
        Entry $entry
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::createNote()
     */
    public function createNote(
        Entry $entry,
        User $user, $submittedNote
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     * @inheritdoc
     */
    public function createScore(
        Score $score
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findEntries()
     */
    public function findEntries()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findEntriesByEntityTypeAndIdentifiers()
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findEntriesByIdentifiers()
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findFeedbackByEntry()
     */
    public function findFeedbackByEntry(
        Entry $entry
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findFeedbackByIdentifier()
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findNoteByEntry()
     */
    public function findNoteByEntry(
        Entry $entry
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::findScoreByEntry()
     */
    public function findScoreByEntry(
        Entry $entry
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity($entityType, DataClass $entity)
    {
        return null;
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId)
    {
        return null;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::initializeFeedback()
     */
    public function initializeFeedback()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::updateNote()
     */
    public function updateNote(Note $note)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::updateScore()
     */
    public function updateScore(Score $score
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity($entityType, $entityIdentifier)
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {

    }

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return [];
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        return false;
    }
}