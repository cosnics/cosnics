<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Feedback;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\FeedbackRepository;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntryFeedbackNotificationJobProcessor;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackService extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\FeedbackService
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\FeedbackRepository
     */
    protected $feedbackRepository;

    /**
     * @var \Chamilo\Core\Queue\Service\JobProducer
     */
    protected $jobProducer;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\FeedbackRepository $feedbackRepository
     * @param \Chamilo\Core\Queue\Service\JobProducer $jobProducer
     */
    public function __construct(FeedbackRepository $feedbackRepository, JobProducer $jobProducer)
    {
        parent::__construct($feedbackRepository);
        $this->jobProducer = $jobProducer;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $feedback
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createFeedback(User $user, $feedback, Entry $entry)
    {
        $feedbackObject = parent::createFeedback($user, $feedback, $entry);
        if($feedbackObject instanceof Feedback)
        {
            $job = new Job();
            $job->setProcessorClass(EntryFeedbackNotificationJobProcessor::class)
                ->setParameter(EntryFeedbackNotificationJobProcessor::PARAM_FEEDBACK_ID, $feedbackObject->getId());

            $this->jobProducer->produceJob($job, 'notifications');
        }

        return $feedbackObject;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByContentObjectPublicationAndEntityType(
        ContentObjectPublication $contentObjectPublication, $entityType
    )
    {
        return $this->feedbackRepository->countDistinctFeedbackByContentObjectPublicationAndEntityType(
            $contentObjectPublication,
            $entityType
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForContentObjectPublicationByEntityTypeAndEntityId(
        ContentObjectPublication $contentObjectPublication,
        $entityType, $entityId
    )
    {
        return $this->feedbackRepository->countFeedbackForContentObjectPublicationByEntityTypeAndEntityId(
            $contentObjectPublication,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType,
        $entityId
    )
    {
        return $this->feedbackRepository->countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication,
            $entityType,
            $entityId
        );
    }

    /**
     * Creates a new instance for a score
     *
     * @return Feedback
     */
    protected function createFeedbackInstance()
    {
        return new Feedback();
    }
}