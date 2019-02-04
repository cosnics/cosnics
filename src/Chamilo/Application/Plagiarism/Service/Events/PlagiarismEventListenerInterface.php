<?php

namespace Chamilo\Application\Plagiarism\Service\Events;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;

/**
 * Interface TurnitinEventListenerInterface
 *
 * @package Chamilo\Application\Plagiarism\Service\Turnitin\Events
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface PlagiarismEventListenerInterface
{
    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $newSubmissionStatus
     */
    public function submissionStatusChanged(SubmissionStatus $newSubmissionStatus);
}