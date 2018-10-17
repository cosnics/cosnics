<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\FeedbackRepository;
use Chamilo\Core\Queue\Service\JobProducer;

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
     * Creates a new instance for a score
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback
     */
    protected function createFeedbackInstance()
    {
        return new Feedback([]);
    }
}