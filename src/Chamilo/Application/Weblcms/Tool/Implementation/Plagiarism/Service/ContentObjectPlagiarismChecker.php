<?php

namespace Chamilo\Application\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPlagiarismChecker
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker
     */
    protected $plagiarismChecker;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService
     */
    protected $contentObjectPlagiarismResultService;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * ContentObjectPlagiarismChecker constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismChecker $plagiarismChecker
     * @param \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismChecker $plagiarismChecker,
        \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService,
        \Chamilo\Core\User\Service\UserService $userService
    )
    {
        $this->plagiarismChecker = $plagiarismChecker;
        $this->contentObjectPlagiarismResultService = $contentObjectPlagiarismResultService;
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     *
     * @throws \Exception
     */
    public function checkContentObjectForPlagiarism(Course $course, ContentObject $contentObject, User $requestUser)
    {
        if (!$this->canCheckForPlagiarism($contentObject))
        {
            throw new PlagiarismException(
                sprintf(
                    'The given content object %s does not represent a valid file object and therefore the content object can not be checked for plagiarism',
                    $contentObject->getId()
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
     * @param int $contentObjectPlagiarismResultId
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewUser
     *
     * @return string
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getPlagiarismViewerUrlForContentObjectById(int $contentObjectPlagiarismResultId, User $viewUser)
    {
        $contentObjectPlagiarismResult =
            $this->contentObjectPlagiarismResultService->findPlagiarismResultById($contentObjectPlagiarismResultId);

        if (!$contentObjectPlagiarismResult instanceof ContentObjectPlagiarismResult)
        {
            throw new PlagiarismException(
                sprintf(
                    'The given content object %s has not been checked for plagiarism yet so the result can not be retrieved'
                )
            );
        }

        return $this->plagiarismChecker->getReportUrlForSubmission(
            $viewUser, $contentObjectPlagiarismResult->getExternalId()
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function canCheckForPlagiarism(ContentObject $contentObject)
    {
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
     */
    public function getRedirectToEULAPageResponse(string $redirectToURL)
    {
        return $this->plagiarismChecker->getRedirectToEULAPageResponse($redirectToURL);
    }

}