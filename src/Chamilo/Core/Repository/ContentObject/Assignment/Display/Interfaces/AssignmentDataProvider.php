<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface AssignmentDataProvider
{

    /**
     *
     * @param integer $entityType
     * @return integer
     */
    public function countDistinctEntriesByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     * @return integer
     */
    public function countDistinctFeedbackByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     * @return integer
     */
    public function countDistinctLateEntriesByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     * @return integer
     */
    public function countEntitiesByEntityType($entityType);

    /**
     *
     * @param integer $entityType
     * @return string
     */
    public function getEntityNameByType($entityType);

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, $entityType);

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     * @param integer $entityId
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTableForEntityTypeAndId(Application $application, $entityType, $entityId);

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType();

    /**
     *
     * @param integer $date
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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @return integer
     */
    public function countEntriesForEntityTypeAndId($entityType, $entityId);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId($entityType, $entityId);

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @return integer
     */
    public function getAverageScoreForEntityTypeAndId($entityType, $entityId);
}