<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\FeedbackRepository;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class FeedbackService
{
    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\FeedbackRepository
     */
    protected $feedbackRepository;

    /**
     * FeedbackService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->feedbackRepository->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->feedbackRepository->retrieveFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @param Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        return $this->feedbackRepository->countFeedbackByEntry($entry);
    }

    /**
     *
     * @param Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Feedback[]
     */
    public function findFeedbackByEntry(Entry $entry)
    {
        return $this->feedbackRepository->findFeedbackByEntry($entry);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return $this->createFeedbackInstance();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteFeedbackForEntry(Entry $entry)
    {
        $this->feedbackRepository->deleteFeedbackForEntry($entry);
    }

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    abstract protected function createFeedbackInstance();

}