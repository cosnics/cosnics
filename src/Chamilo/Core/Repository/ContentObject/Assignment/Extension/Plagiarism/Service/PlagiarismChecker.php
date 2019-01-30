<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
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

        if ($result->isInProgress())
        {
            $this->requestStatusUpdateForPlagiarismCheck($result);

            return;
        }

        if ($result->isFailed() && $result->canRetry())
        {
            $this->retryPlagiarismCheck($result, $assignment, $entry, $entryPlagiarismResultServiceBridge);

            return;
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
     */
    protected function requestStatusUpdateForPlagiarismCheck(EntryPlagiarismResult $entryPlagiarismResult)
    {

    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     */
    protected function retryPlagiarismCheck(
        EntryPlagiarismResult $entryPlagiarismResult, Assignment $assignment, Entry $entry,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {

    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
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
        $this->plagiarismChecker->createViewerLaunchURL(
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