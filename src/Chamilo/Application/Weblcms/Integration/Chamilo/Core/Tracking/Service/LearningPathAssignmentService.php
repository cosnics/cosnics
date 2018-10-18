<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Service\EntryNotificationJobProcessor;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathAssignmentEphorusRepository;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathAssignmentRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;

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
     * @var \Chamilo\Core\Queue\Service\JobProducer
     */
    protected $jobProducer;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathAssignmentRepository $assignmentRepository
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathAssignmentEphorusRepository $learningPathAssignmentEphorusRepository
     * @param \Chamilo\Core\Queue\Service\JobProducer $jobProducer
     */
    public function __construct(LearningPathAssignmentRepository $assignmentRepository, LearningPathAssignmentEphorusRepository $learningPathAssignmentEphorusRepository, JobProducer $jobProducer)
    {
        parent::__construct($assignmentRepository, $learningPathAssignmentEphorusRepository);
        $this->jobProducer = $jobProducer;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAddress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createEntry(
        TreeNodeData $treeNodeData, TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId, $userId, $contentObjectId,
        $ipAddress
    )
    {
        /** @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt $treeNodeAttempt */
        $entry = parent::createEntry($treeNodeData, $treeNodeAttempt, $entityType, $entityId, $userId, $contentObjectId, $ipAddress);
        if($entry instanceof Entry)
        {
            $job = new Job();
            $job->setProcessorClass(EntryNotificationJobProcessor::class)
                ->setParameter(EntryNotificationJobProcessor::PARAM_ENTRY_ID, $entry->getId())
                ->setParameter(EntryNotificationJobProcessor::PARAM_CONTENT_OBJECT_PUBLICATION_ID, $treeNodeAttempt->get_publication_id());

            $this->jobProducer->produceJob($job, 'notifications');
        }

        return $entry;
    }

    /**
     * Creates a new instance for an entry
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry
     */
    protected function createEntryInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry(
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\EntryAttachment|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    protected function createEntryAttachmentInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\EntryAttachment();
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score
     */
    protected function createScoreInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score(
        );
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback
     */
    protected function createFeedbackInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback(
        );
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note
     */
    protected function createNoteInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note(
        );
    }

    /**
     * @return string
     */
    public function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry::class;
    }

    /**
     * @return string
     */
    public function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score::class;
    }

    /**
     * @return string
     */
    public function getFeedbackClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback::class;
    }
}