<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
use Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Service
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
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

        /** @var File $contentObject */
        $contentObject = $this->contentObjectRepository->findById($entry->getContentObjectId());

        $assignmentOwner = new User();
        $assignmentOwner->setId($assignment->get_owner_id());

        $entryOwner = new User();
        $entryOwner->setId($entry->getUserId());

        $submissionId = $this->plagiarismChecker->uploadFile(
            $assignmentOwner, $entryOwner, $contentObject->get_title(), $contentObject->get_full_path(),
            $contentObject->get_filename()
        );

        $entryPlagiarismResultServiceBridge->createEntryPlagiarismResultForEntry($entry, $submissionId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
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