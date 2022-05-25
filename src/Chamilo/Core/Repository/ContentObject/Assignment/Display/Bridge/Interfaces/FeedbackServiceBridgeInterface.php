<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Interfaces
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface FeedbackServiceBridgeInterface
{
    /**
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByEntityType($entityType);

    /**
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId);

    /**
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId);

    /**
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier);

    /**
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function getFeedbackByIdentifier($feedbackIdentifier);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback>
     */
    public function getFeedbackByEntry(Entry $entry);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $feedback
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function createFeedback(User $user, $feedback, Entry $entry);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function updateFeedback(Feedback $feedback);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback);
}