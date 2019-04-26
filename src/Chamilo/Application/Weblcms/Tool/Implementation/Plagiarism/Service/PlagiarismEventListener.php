<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventListenerInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismEventListener implements PlagiarismEventListenerInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service\ContentObjectPlagiarismResultService
     */
    protected $contentObjectPlagiarismResultService;

    /**
     * PlagiarismEventListener constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service\ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service\ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService
    )
    {
        $this->contentObjectPlagiarismResultService = $contentObjectPlagiarismResultService;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $newSubmissionStatus
     */
    public function submissionStatusChanged(SubmissionStatus $newSubmissionStatus)
    {
        $contentObjectPlagiarismResult = $this->contentObjectPlagiarismResultService->findPlagiarismResultByExternalId(
            $newSubmissionStatus->getSubmissionId()
        );

        if(empty($contentObjectPlagiarismResult))
        {
            return;
        }

        $contentObjectPlagiarismResult->copyFromSubmissionStatus($newSubmissionStatus);
        $this->contentObjectPlagiarismResultService->updateContentObjectPlagiarismResult($contentObjectPlagiarismResult);
    }

}