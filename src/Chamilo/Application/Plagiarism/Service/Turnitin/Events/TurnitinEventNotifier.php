<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin\Events;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin\Events
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinEventNotifier implements TurnitinEventListenerInterface
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\Events\TurnitinEventListenerInterface[]
     */
    protected $eventListeners;

    /**
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\Events\TurnitinEventListenerInterface $turnitinEventListener
     */
    public function addEventListener(TurnitinEventListenerInterface $turnitinEventListener)
    {
        $this->eventListeners[] = $turnitinEventListener;
    }

    /**
     * @param string $submissionId
     * @param bool $isError
     * @param string $errorCode
     */
    public function submissionUploadProcessed(string $submissionId, bool $isError = false, string $errorCode = '')
    {
        foreach($this->eventListeners as $eventListener)
        {
            $eventListener->submissionUploadProcessed($submissionId, $isError, $errorCode);
        }
    }

    /**
     * @param string $submissionId
     * @param int $overallMatchPercentage
     */
    public function similarityReportGenerated(string $submissionId, int $overallMatchPercentage)
    {
        foreach($this->eventListeners as $eventListener)
        {
            $eventListener->similarityReportGenerated($submissionId, $overallMatchPercentage);
        }
    }
}