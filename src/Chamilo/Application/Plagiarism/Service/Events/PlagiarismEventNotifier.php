<?php

namespace Chamilo\Application\Plagiarism\Service\Events;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin\Events
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismEventNotifier implements PlagiarismEventListenerInterface
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventListenerInterface[]
     */
    protected $eventListeners;

    /**
     * @param \Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventListenerInterface $turnitinEventListener
     */
    public function addEventListener(PlagiarismEventListenerInterface $turnitinEventListener)
    {
        $this->eventListeners[] = $turnitinEventListener;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $newSubmissionStatus
     */
    public function submissionStatusChanged(SubmissionStatus $newSubmissionStatus)
    {
        foreach($this->eventListeners as $eventListener)
        {
            $eventListener->submissionStatusChanged($newSubmissionStatus);
        }
    }
}