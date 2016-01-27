<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Table\Entity\EntityTable;
use Chamilo\Libraries\Architecture\Application\Application;

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
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getEntityNameByType()
     */
    public function getEntityNameByType($entityType)
    {
        return Translation :: get('User');
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
        // TODO Auto-generated method stub
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
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider::getEntryTableForEntityTypeAndId()
     */
    public function getEntryTableForEntityTypeAndId(\Chamilo\Libraries\Architecture\Application\Application $application,
        $entityType, $entityId)
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
}