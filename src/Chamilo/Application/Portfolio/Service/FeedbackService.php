<?php
namespace Chamilo\Application\Portfolio\Service;

use Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository;

/**
 *
 * @package Chamilo\Application\Portfolio\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FeedbackService
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository
     */
    private $feedbackRepository;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository
     */
    public function getFeedbackRepository()
    {
        return $this->feedbackRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function setFeedbackRepository(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }
}

