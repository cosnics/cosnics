<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventListenerInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PlagiarismEventListener implements PlagiarismEventListenerInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service\EntryPlagiarismResultService
     */
    protected $entryPlagiarismResultService;

    /**
     * PlagiarismEventListener constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service\EntryPlagiarismResultService
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service\EntryPlagiarismResultService $entryPlagiarismResultService
    )
    {
        $this->entryPlagiarismResultService = $entryPlagiarismResultService;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $newSubmissionStatus
     */
    public function submissionStatusChanged(SubmissionStatus $newSubmissionStatus)
    {
        $entryPlagiarismResult = $this->entryPlagiarismResultService->findEntryPlagiarismResultByExternalId(
            $newSubmissionStatus->getSubmissionId()
        );

        if(empty($entryPlagiarismResult))
        {
            return;
        }

        $entryPlagiarismResult->copyFromSubmissionStatus($newSubmissionStatus);
        $this->entryPlagiarismResultService->updateEntryPlagiarismResult($entryPlagiarismResult);
    }
}