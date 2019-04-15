<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismChecker
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface
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
     * @param \Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface $plagiarismChecker
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface $plagiarismChecker,
        \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository,
        \Chamilo\Core\User\Service\UserService $userService
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
        if (!$this->canCheckForPlagiarism($entry))
        {
            throw new PlagiarismException(
                sprintf(
                    'The given entry %s does not represent a valid file object and therefore the entry can not be checked for plagiarism',
                    $entry->getId()
                )
            );
        }

        $entryPlagiarismResult = $entryPlagiarismResultServiceBridge->findEntryPlagiarismResultByEntry($entry);

        try
        {
            $currentSubmissionStatus = null;

            if ($entryPlagiarismResult instanceof EntryPlagiarismResult)
            {
                $currentSubmissionStatus = $entryPlagiarismResult->getSubmissionStatus();
            }
            else
            {
                $entryPlagiarismResult = null;
            }

            /** @var File $contentObject */
            $contentObject = $this->contentObjectRepository->findById($entry->getContentObjectId());

            $assignmentOwner = $this->userService->findUserByIdentifier($assignment->get_owner_id());
            $entryOwner = $this->userService->findUserByIdentifier($entry->getUserId());

            $newStatus = $this->plagiarismChecker->checkForPlagiarism(
                $entryOwner, $assignmentOwner, $contentObject->get_title(),
                $contentObject->get_full_path(), $contentObject->get_filename(), $currentSubmissionStatus
            );

            if (empty($currentSubmissionStatus) || $currentSubmissionStatus->getStatus() != $newStatus->getStatus())
            {
                $this->updateResultWithStatus(
                    $entryPlagiarismResultServiceBridge, $entry, $newStatus, $entryPlagiarismResult
                );
            }
        }
        catch (PlagiarismException $exception)
        {
            if ($entryPlagiarismResult instanceof EntryPlagiarismResult)
            {
                $entryPlagiarismResult->setStatus(SubmissionStatus::STATUS_FAILED);
                $entryPlagiarismResult->setError(SubmissionStatus::ERROR_UNKNOWN);
                $entryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($entryPlagiarismResult);
            }

            throw $exception;
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $newStatus
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult|null $entryPlagiarismResult
     */
    protected function updateResultWithStatus(
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge,
        Entry $entry, SubmissionStatus $newStatus, EntryPlagiarismResult $entryPlagiarismResult = null
    )
    {
        if (!empty($entryPlagiarismResult))
        {
            $entryPlagiarismResult->copyFromSubmissionStatus($newStatus);
            $entryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($entryPlagiarismResult);
        }
        else
        {
            $entryPlagiarismResultServiceBridge->createEntryPlagiarismResultForEntry(
                $entry, $newStatus->getSubmissionId()
            );
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
                sprintf(
                    'The given entry %s has not been checked for plagiarism yet so the result can not be retrieved',
                    $entry->getId()
                )
            );
        }

        return $this->plagiarismChecker->getReportUrlForSubmission($user, $entryPlagiarismResult->getExternalId());
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

        return $this->plagiarismChecker->canCheckForPlagiarism(
            $contentObject->get_full_path(), $contentObject->get_filename()
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
        return $this->plagiarismChecker->getRedirectToEULAPageResponse($redirectToURL);
    }

    /**
     * @return bool
     */
    public function isInMaintenanceMode()
    {
        return $this->plagiarismChecker->isInMaintenanceMode();
    }

}