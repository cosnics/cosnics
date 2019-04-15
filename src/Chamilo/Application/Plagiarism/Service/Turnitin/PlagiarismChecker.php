<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\InvalidFileException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
use Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings;
use Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismChecker implements PlagiarismCheckerInterface
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionService
     */
    protected $submissionService;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * PlagiarismChecker constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionService $submissionService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionService $submissionService,
        \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository,
        UserService $userService,
        ConfigurationConsulter $configurationConsulter
    )
    {
        $this->submissionService = $submissionService;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->userService = $userService;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $currentSubmissionStatus
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param \Chamilo\Core\User\Storage\DataClass\User $submitter
     * @param string $title
     * @param string $filePath
     * @param string $filename
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     * @throws \Exception
     */
    public function checkForPlagiarism(
        User $owner, User $submitter, string $title, string $filePath,
        string $filename, SubmissionStatus $currentSubmissionStatus = null
    )
    {
        if (!$this->canCheckForPlagiarism($title, $filePath))
        {
            throw new PlagiarismException(
                sprintf(
                    'The given file %s can not be checked for plagiarism', $filePath
                )
            );
        }

        if (empty($currentSubmissionStatus) || empty($currentSubmissionStatus->getSubmissionId()))
        {
            return $this->requestNewPlagiarismCheck($owner, $submitter, $title, $filePath, $filename);
        }

        if ($currentSubmissionStatus->isUploadInProgress())
        {
            return $this->requestUploadProgressStatusUpdate($currentSubmissionStatus);
        }

        if ($currentSubmissionStatus->isUploadComplete())
        {
            return $this->requestSimilarityCheckForResult($currentSubmissionStatus);
        }

        if ($currentSubmissionStatus->isReportGenerationInProgress())
        {
            return $this->requestReportGenerationStatusUpdate($currentSubmissionStatus);
        }

        if ($currentSubmissionStatus->isFailed() && $currentSubmissionStatus->canRetry())
        {
            return $this->retryPlagiarismCheck($currentSubmissionStatus, $filePath, $filename);
        }

        throw new PlagiarismException(
            sprintf(
                'The given submission status is not in a valid state and can not be processed (%s)',
                $currentSubmissionStatus->getStatus()
            )
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param \Chamilo\Core\User\Storage\DataClass\User $submitter
     * @param string $title
     * @param string $filePath
     * @param string $filename
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function requestNewPlagiarismCheck(
        User $submitter, User $owner, string $title, string $filePath, string $filename
    )
    {
        if (!$this->submissionService->canUploadFile($filePath, $filename))
        {
            throw new InvalidFileException($filePath, $filename);
        }

        $submissionId = $this->submissionService->uploadFile($submitter, $owner, $title, $filePath, $filename);

        $status = new SubmissionStatus($submissionId, SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS);

        return $status;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $currentSubmissionStatus
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     * @throws \Exception
     */
    protected function requestUploadProgressStatusUpdate(SubmissionStatus $currentSubmissionStatus)
    {
        if (!$currentSubmissionStatus->isUploadInProgress())
        {
            return $currentSubmissionStatus;
        }

        $progressStatus = $this->submissionService->getUploadStatus($currentSubmissionStatus->getSubmissionId());
        if ($progressStatus->isUploadComplete())
        {
            return $this->requestSimilarityCheckForResult($currentSubmissionStatus);
        }

        return $progressStatus;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $currentSubmissionStatus
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Exception
     */
    protected function requestReportGenerationStatusUpdate(SubmissionStatus $currentSubmissionStatus)
    {
        if (!$currentSubmissionStatus->isReportGenerationInProgress())
        {
            return $currentSubmissionStatus;
        }

        return $this->submissionService->getSimilarityReportStatus($currentSubmissionStatus->getSubmissionId());
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $currentSubmissionStatus
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function requestSimilarityCheckForResult(SubmissionStatus $currentSubmissionStatus)
    {
        $settings = new SimilarityReportSettings();
        $settings->setSearchRepositories(
            [
                SimilarityReportSettings::SEARCH_REPOSITORY_INTERNET,
                SimilarityReportSettings::SEARCH_REPOSITORY_PUBLICATION,
                SimilarityReportSettings::SEARCH_REPOSITORY_SUBMITTED_WORK
            ]
        );

        $settings->setAutoExcludeMatchingScope(SimilarityReportSettings::AUTO_EXCLUDE_ALL);

        $this->submissionService->generateSimilarityReport($currentSubmissionStatus->getSubmissionId(), $settings);

        $submissionStatus = new SubmissionStatus(
            $currentSubmissionStatus->getSubmissionId(), SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS
        );

        return $submissionStatus;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $currentSubmissionStatus
     * @param string $filePath
     * @param string $filename
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     * @throws \Exception
     */
    protected function retryPlagiarismCheck(
        SubmissionStatus $currentSubmissionStatus, string $filePath, string $filename
    )
    {
        if (!$currentSubmissionStatus->isFailed())
        {
            return $currentSubmissionStatus;
        }

        if (!$this->submissionService->canUploadFile($filePath, $filename))
        {
            throw new InvalidFileException($filePath, $filename);
        }

        $uploadStatus = $this->submissionService->getUploadStatus($currentSubmissionStatus->getSubmissionId());
        if ($uploadStatus->isUploadComplete())
        {
            return $this->requestSimilarityCheckForResult($currentSubmissionStatus);
        }

        $this->submissionService->uploadFileForSubmission(
            $currentSubmissionStatus->getSubmissionId(), $filePath, $filename
        );

        $status = new SubmissionStatus(
            $currentSubmissionStatus->getSubmissionId(), SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS
        );

        return $status;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submissionId
     *
     * @return string
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getReportUrlForSubmission(User $user, string $submissionId)
    {
        $viewerLaunchSettings = new ViewerLaunchSettings();
        $viewerLaunchSettings->setViewerDefaultPermissionSet(ViewerLaunchSettings::DEFAULT_PERMISSION_SET_INSTRUCTOR);

        return $this->submissionService->createViewerLaunchURL(
            $submissionId, $user, $viewerLaunchSettings
        );
    }

    /**
     * @param string $redirectToURL
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getRedirectToEULAPageResponse(string $redirectToURL)
    {
        return $this->submissionService->getRedirectToEULAPageResponse($redirectToURL);
    }

    /**
     * @param string $filePath
     * @param string $filename
     *
     * @return bool
     */
    public function canCheckForPlagiarism(string $filePath, string $filename)
    {
        return !$this->isInMaintenanceMode() &&
            $this->submissionService->canUploadFile($filePath, $filename);
    }

    /**
     * @return bool
     */
    public function isInMaintenanceMode()
    {
        return $this->configurationConsulter->getSetting(['Chamilo\Application\Plagiarism', 'maintenance_mode']) == 1;
    }
}