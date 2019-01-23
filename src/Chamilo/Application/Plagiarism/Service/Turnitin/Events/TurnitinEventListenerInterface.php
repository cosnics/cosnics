<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin\Events;

/**
 * Interface TurnitinEventListenerInterface
 *
 * @package Chamilo\Application\Plagiarism\Service\Turnitin\Events
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface TurnitinEventListenerInterface
{
    /**
     * @param string $submissionId
     * @param bool $isError
     * @param string $errorCode
     */
    public function submissionUploadProcessed(string $submissionId, bool $isError = false, string $errorCode = '');

    /**
     * @param string $submissionId
     * @param int $overallMatchPercentage
     */
    public function similarityReportGenerated(string $submissionId, int $overallMatchPercentage);
}