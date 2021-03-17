<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssignmentServiceBridge implements AssignmentServiceBridgeInterface
{

    public function initializeScore()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countDistinctEntriesByEntityType()
     */
    public function countDistinctEntriesByEntityType($entityType)
    {
        return 1;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countDistinctFeedbackByEntityType()
     */
    public function countDistinctFeedbackByEntityType($entityType)
    {
        return 0;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countDistinctLateEntriesByEntityType()
     */
    public function countDistinctLateEntriesByEntityType($entityType)
    {
        return 0;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countEntitiesByEntityType()
     *
     * @param $entityType
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntitiesByEntityType($entityType, FilterParameters $filterParameters)
    {
        return 10;
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::getPluralEntityNameByType()
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::getEntityTableForType()
     */
    public function getEntityTableForType(Application $application, EntityTableParameters $entityTableParameters)
    {
        $entityTableParameters->setEntityHasMultipleMembers(false);
        $entityTableParameters->setEntityClass(User::class);
        $entityTableParameters->setEntityProperties(['name']);

        return new EntityTable($application, $entityTableParameters);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::canEditAssignment()
     */
    public function canEditAssignment()
    {
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countDistinctFeedbackForEntityTypeAndId()
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countDistinctScoreForEntityTypeAndId()
     */
    public function countDistinctScoreForEntityTypeAndId($entityType, $entityId)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countEntriesForEntityTypeAndId()
     */
    public function countEntriesForEntityTypeAndId($entityType, $entityId, Condition $condition = null)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countFeedbackByEntityTypeAndEntityId()
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countFeedbackByEntryIdentifier()
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::createEntry()
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findEntryByIdentifier()
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::getAverageScoreForEntityTypeAndId()
     */
    public function getAverageScoreForEntityTypeAndId($entityType, $entityId)
    {
        return null;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::getAverageScoreForEntityTypeAndId()
     */
    public function getLastScoreForEntityTypeAndId($entityType, $entityId)
    {
        return null;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::getCurrentEntityType()
     */
    public function getCurrentEntityType()
    {
        return 0;
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::getEntryTableForEntityTypeAndId()
     */
    public function getEntryTableForEntityTypeAndId(
        \Chamilo\Libraries\Architecture\Application\Application $application, EntryTableParameters $entryTableParameters
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::isDateAfterAssignmentEndTime()
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::countFeedbackByEntry()
     */
    public function countFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::createNote()
     */
    public function createNote(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry,
        \Chamilo\Core\User\Storage\DataClass\User $user, $submittedNote
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findEntries()
     */
    public function findEntries()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findEntriesByEntityTypeAndIdentifiers()
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findEntriesByIdentifiers()
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findFeedbackByEntry()
     */
    public function findFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findFeedbackByIdentifier()
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findNoteByEntry()
     */
    public function findNoteByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
    )
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::findScoreByEntry()
     */
    public function findScoreByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
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
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::initializeFeedback()
     */
    public function initializeFeedback()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::updateNote()
     */
    public function updateNote(\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Note $note)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::updateScore()
     */
    public function updateScore(\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
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

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return mixed
     */
    public function findEntitiesByEntityType(int $entityType, FilterParameters $filterParameters)
    {
        $users = array();

        for ($i = 1; $i <= 10; $i ++)
        {
            $user = array();
            $user['name'] = 'Preview User ' . $i;
            // $user = new User();
            // $user->set_lastname('User');
            // $user->set_firstname('Test ' . $i);
            // $user->set_email('test.' . $i . '@user.com');
            // $user->set_username('test.' . $i . '@user.com');

            $users[] = $user;
        }

        return new ArrayResultSet($users);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId)
    {
        // TODO: Implement getUsersForEntity() method.
    }

    /**
     * @param int $entityType
     * @param int $entityId
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesForEntityTypeAndId(
        int $entityType, int $entityId, Condition $condition = null, int $offset = null, int $count = null,
        array $orderProperty = []
    )
    {
       return [];
    }

    public function areSubmissionsAllowed()
    {
        return false;
    }
}
