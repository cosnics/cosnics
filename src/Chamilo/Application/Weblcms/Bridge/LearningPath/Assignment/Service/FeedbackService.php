<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\FeedbackRepository;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentJobProcessor;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Service\EntryFeedbackNotificationJobProcessor;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackService extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service\FeedbackService
{
    /**
     * @var \Chamilo\Core\Queue\Service\JobProducer
     */
    protected $jobProducer;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\FeedbackRepository $feedbackRepository
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
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createFeedbackForPublicationAndEntry(User $user, $feedback, Entry $entry, ContentObjectPublication $contentObjectPublication)
    {
        $feedbackObject = parent::createFeedback($user, $feedback, $entry);
        if($feedbackObject instanceof Feedback)
        {
            $job = new Job();
            $job->setProcessorClass(EntryFeedbackNotificationJobProcessor::class)
                ->setParameter(EntryFeedbackNotificationJobProcessor::PARAM_CONTENT_OBJECT_PUBLICATION_ID, $contentObjectPublication->getId())
                ->setParameter(EntryFeedbackNotificationJobProcessor::PARAM_FEEDBACK_ID, $feedbackObject->getId());

            $this->jobProducer->produceJob($job, 'notifications');
        }

        return $feedbackObject;
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback
     */
    protected function createFeedbackInstance()
    {
        return new Feedback();
    }
}