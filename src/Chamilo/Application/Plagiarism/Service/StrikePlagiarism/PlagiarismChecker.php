<?php

namespace Chamilo\Application\Plagiarism\Service\StrikePlagiarism;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Service\Base\PlagiarismCheckerBase;
use Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\User;

class PlagiarismChecker extends PlagiarismCheckerBase implements PlagiarismCheckerInterface
{
    const REPORT_TEMPLATE_URL = 'https://lmsapi.plagiat.pl/report/?auth=__TOKEN__';

    protected SubmissionService $submissionService;

    public function __construct(SubmissionService $submissionService, ConfigurationConsulter $configurationConsulter)
    {
        parent::__construct($configurationConsulter);
        $this->submissionService = $submissionService;
    }

    public function checkForPlagiarism(
        User $owner, User $submitter, string $title, string $filePath, string $filename,
        SubmissionStatus $currentSubmissionStatus = null
    )
    {
        if (!$this->canCheckForPlagiarism($filePath, $filename))
        {
            throw new PlagiarismException(
                sprintf(
                    'The given file %s can not be checked for plagiarism', $filePath
                )
            );
        }

        if (empty($currentSubmissionStatus) || empty($currentSubmissionStatus->getSubmissionId()))
        {
            return $this->requestNewPlagiarismCheck($submitter, $owner, $title, $filePath, $filename);
        }

        if ($currentSubmissionStatus->isReportGenerationInProgress())
        {
            return $this->requestReportGenerationStatusUpdate($currentSubmissionStatus);
        }

        if ($currentSubmissionStatus->isFailed() && $currentSubmissionStatus->canRetry())
        {
            return $this->requestReportGenerationStatusUpdate($currentSubmissionStatus);
        }

        if($currentSubmissionStatus->isReportGenerated())
        {
            return $currentSubmissionStatus;
        }

        throw new PlagiarismException(
            sprintf(
                'The given submission status is not in a valid state and can not be processed (%s)',
                $currentSubmissionStatus->getStatus()
            )
        );
    }

    protected function requestNewPlagiarismCheck(
        User $submitter, User $owner, string $title, string $filePath, string $filename
    )
    {
        $submissionId = $this->submissionService->uploadDocument($submitter, $owner, $title, $filePath, $filename);

        $status = new SubmissionStatus($submissionId, SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS);

        return $status;
    }

    protected function requestReportGenerationStatusUpdate(SubmissionStatus $currentSubmissionStatus)
    {
        if (!$currentSubmissionStatus->isReportGenerationInProgress())
        {
            return $currentSubmissionStatus;
        }

        try {
            $documentMetadata = $this->submissionService->getDocumentMetadata($currentSubmissionStatus->getSubmissionId());
        } catch(\Exception $ex) {var_dump($ex->getMessage());}

        if($documentMetadata->isChecked())
        {
            return new SubmissionStatus(
                $currentSubmissionStatus->getSubmissionId(), SubmissionStatus::STATUS_REPORT_GENERATED,
                round($documentMetadata->getFactor1() * 100)
            );
        }

        return $currentSubmissionStatus;
    }

    public function getReportUrlForSubmission(User $user, string $submissionId)
    {
        $token = $this->submissionService->getViewReportToken($submissionId);

        return str_replace('__TOKEN__', $token, self::REPORT_TEMPLATE_URL);
    }

    public function getRedirectToEULAPageResponse(string $redirectToURL): ?string
    {
        return '';
    }
}