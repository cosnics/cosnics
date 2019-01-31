<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SubmissionStatus;
use Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismChecker
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker
     */
    protected $plagiarismChecker;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * PlagiarismChecker constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker $plagiarismChecker
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker $plagiarismChecker,
        \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository,
        UserService $userService
    )
    {
        $this->plagiarismChecker = $plagiarismChecker;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     * @throws \Exception
     */
    public function checkEntryForPlagiarism(
        Assignment $assignment, Entry $entry,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        $result = $entryPlagiarismResultServiceBridge->findEntryPlagiarismResultByEntry($entry);
        if (!$result instanceof EntryPlagiarismResult)
        {
            $this->requestNewPlagiarismCheck($assignment, $entry, $entryPlagiarismResultServiceBridge);

            return;
        }

        try
        {
            $submissionStatus = $result->getSubmissionStatus();

            if ($submissionStatus->isUploadInProgress())
            {
                $this->requestUploadProgressStatusUpdate($result, $entryPlagiarismResultServiceBridge);

                return;
            }

            if ($submissionStatus->isUploadComplete())
            {
                $this->requestSimilarityCheckForResult($result, $entryPlagiarismResultServiceBridge);

                return;
            }

            if ($submissionStatus->isReportGenerationInProgress())
            {
                $this->requestReportGenerationStatusUpdate($result, $entryPlagiarismResultServiceBridge);

                return;
            }

            if ($submissionStatus->isFailed() && $submissionStatus->canRetry())
            {
                $this->retryPlagiarismCheck($result, $entry, $entryPlagiarismResultServiceBridge);

                return;
            }
        }
        catch (PlagiarismException $exception)
        {
            $result->setStatus(SubmissionStatus::STATUS_FAILED);
            $result->setError(SubmissionStatus::ERROR_UNKNOWN);
            $entryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($result);

            throw $exception;
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function requestNewPlagiarismCheck(
        Assignment $assignment, Entry $entry,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        if (!$this->canCheckForPlagiarism($entry))
        {
            throw new PlagiarismException(
                sprintf(
                    'The given entry %s does not represent a valid file object and therefore the entry can not be checked for plagiarism',
                    $entry->getId()
                )
            );
        }

        /** @var File $contentObject */
        $contentObject = $this->contentObjectRepository->findById($entry->getContentObjectId());

        $assignmentOwner = $this->userService->findUserByIdentifier($assignment->get_owner_id());
        $entryOwner = $this->userService->findUserByIdentifier($entry->getUserId());

        $submissionId = $this->plagiarismChecker->uploadFile(
            $assignmentOwner, $entryOwner, $contentObject->get_title(), $contentObject->get_full_path(),
            $contentObject->get_filename()
        );

        $entryPlagiarismResultServiceBridge->createEntryPlagiarismResultForEntry($entry, $submissionId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Exception
     */
    protected function requestUploadProgressStatusUpdate(
        EntryPlagiarismResult $entryPlagiarismResult,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        $submissionStatus = $entryPlagiarismResult->getSubmissionStatus();

        if (!$submissionStatus->isUploadInProgress())
        {
            return;
        }

        $progressStatus = $this->plagiarismChecker->getUploadStatus($entryPlagiarismResult->getExternalId());
        if ($progressStatus->isUploadComplete())
        {
            $this->requestSimilarityCheckForResult($entryPlagiarismResult, $entryPlagiarismResultServiceBridge);

            return;
        }

        if (!$progressStatus->isInProgress())
        {
            $entryPlagiarismResult->copyFromSubmissionStatus($progressStatus);
            $entryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($entryPlagiarismResult);
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Exception
     */
    protected function requestReportGenerationStatusUpdate(
        EntryPlagiarismResult $entryPlagiarismResult,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        $submissionStatus = $entryPlagiarismResult->getSubmissionStatus();

        if (!$submissionStatus->isReportGenerationInProgress())
        {
            return;
        }

        $progressStatus = $this->plagiarismChecker->getSimilarityReportStatus($entryPlagiarismResult->getExternalId());
        if (!$progressStatus->isInProgress())
        {
            $entryPlagiarismResult->copyFromSubmissionStatus($progressStatus);
            $entryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($entryPlagiarismResult);
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function requestSimilarityCheckForResult(
        EntryPlagiarismResult $entryPlagiarismResult,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
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

        $this->plagiarismChecker->generateSimilarityReport(
            $entryPlagiarismResult->getExternalId(), $settings
        );

        $entryPlagiarismResult->setStatus(SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS);
        $entryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($entryPlagiarismResult);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     * @throws \Exception
     */
    protected function retryPlagiarismCheck(
        EntryPlagiarismResult $entryPlagiarismResult, Entry $entry,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        if (!$this->canCheckForPlagiarism($entry))
        {
            throw new PlagiarismException(
                sprintf(
                    'The given entry %s does not represent a valid file object and therefore the entry can not be checked for plagiarism',
                    $entry->getId()
                )
            );
        }

        $submissionStatus = $entryPlagiarismResult->getSubmissionStatus();

        if (!$submissionStatus->isFailed())
        {
            return;
        }

        $uploadStatus = $this->plagiarismChecker->getUploadStatus($entryPlagiarismResult->getExternalId());
        if ($uploadStatus->isUploadComplete())
        {
            $this->requestSimilarityCheckForResult($entryPlagiarismResult, $entryPlagiarismResultServiceBridge);
        }
        else
        {
            /** @var File $contentObject */
            $contentObject = $this->contentObjectRepository->findById($entry->getContentObjectId());

            $this->plagiarismChecker->uploadFileForSubmission(
                $entryPlagiarismResult->getExternalId(), $contentObject->get_full_path(), $contentObject->get_filename()
            );

            $entryPlagiarismResult->setStatus(SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS);
            $entryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($entryPlagiarismResult);
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @return string
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getPlagiarismViewerUrlForEntry(
        User $user, Entry $entry,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        $entryPlagiarismResult = $entryPlagiarismResultServiceBridge->findEntryPlagiarismResultByEntry($entry);
        if (!$entryPlagiarismResult instanceof EntryPlagiarismResult)
        {
            throw new PlagiarismException(
                sprintf('The given entry %s has not been checked for plagiarism yet so the result can not be retrieved')
            );
        }

        $viewerLaunchSettings = new ViewerLaunchSettings();
        $viewerLaunchSettings->setViewerDefaultPermissionSet(ViewerLaunchSettings::DEFAULT_PERMISSION_SET_INSTRUCTOR);

        return $this->plagiarismChecker->createViewerLaunchURL(
            $entryPlagiarismResult->getExternalId(), $user, $viewerLaunchSettings
        );
    }

    /**
     * @param string $externalId
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function generateSimilarityReport($externalId)
    {
        $similarityReportSettings = new SimilarityReportSettings();

        $this->plagiarismChecker->generateSimilarityReport($externalId, $similarityReportSettings);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function canCheckForPlagiarism(Entry $entry)
    {
        $contentObject = $this->contentObjectRepository->findById($entry->getContentObjectId());
        if (!$contentObject instanceof File)
        {
            return false;
        }

        return $this->plagiarismChecker->canUploadFile($contentObject->get_full_path(), $contentObject->get_filename());
    }

    /**
     * @param string $redirectToURL
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getRedirectToEULAPageResponse(string $redirectToURL)
    {
        return $this->plagiarismChecker->getRedirectToEULAPageResponse($redirectToURL);
    }

}