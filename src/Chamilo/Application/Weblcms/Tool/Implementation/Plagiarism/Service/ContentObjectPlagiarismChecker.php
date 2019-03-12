<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
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
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service\ContentObjectPlagiarismResultService
     */
    protected $contentObjectPlagiarismResultService;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * ContentObjectPlagiarismChecker constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface $plagiarismChecker
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service\ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface $plagiarismChecker,
        ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService,
        \Chamilo\Core\User\Service\UserService $userService, ContentObjectRepository $contentObjectRepository
    )
    {
        $this->plagiarismChecker = $plagiarismChecker;
        $this->contentObjectPlagiarismResultService = $contentObjectPlagiarismResultService;
        $this->userService = $userService;
        $this->contentObjectRepository = $contentObjectRepository;
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

        $contentObjectPlagiarismResult =
            $this->contentObjectPlagiarismResultService->findPlagiarismResultByContentObject($course, $contentObject);

        try
        {
            $currentSubmissionStatus = null;

            if ($contentObjectPlagiarismResult instanceof ContentObjectPlagiarismResult)
            {
                $currentSubmissionStatus = $contentObjectPlagiarismResult->getSubmissionStatus();
            }
            else
            {
                $contentObjectPlagiarismResult = null;
            }

            $contentObjectOwner = $this->userService->findUserByIdentifier($contentObject->get_owner_id());

            /** @var File $contentObject */
            $newStatus = $this->plagiarismChecker->checkForPlagiarism(
                $contentObjectOwner, $requestUser, $contentObject->get_title(),
                $contentObject->get_full_path(), $contentObject->get_filename(), $currentSubmissionStatus
            );

            if (empty($currentSubmissionStatus) || $currentSubmissionStatus->getStatus() != $newStatus->getStatus())
            {
                $this->updateResultWithStatus(
                    $course, $contentObject, $newStatus, $contentObjectPlagiarismResult, $requestUser
                );
            }
        }
        catch (PlagiarismException $exception)
        {
            if ($contentObjectPlagiarismResult instanceof ContentObjectPlagiarismResult)
            {
                $contentObjectPlagiarismResult->setStatus(SubmissionStatus::STATUS_FAILED);
                $contentObjectPlagiarismResult->setError(SubmissionStatus::ERROR_UNKNOWN);

                $this->contentObjectPlagiarismResultService->updateContentObjectPlagiarismResult(
                    $contentObjectPlagiarismResult
                );
            }

            throw $exception;
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $contentObjectIds
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     *
     * @throws \Exception
     */
    public function checkContentObjectsForPlagiarismById(Course $course, array $contentObjectIds, User $requestUser)
    {
        foreach ($contentObjectIds as $contentObjectId)
        {
            $contentObject = $this->contentObjectRepository->findById($contentObjectId);
            if ($contentObject instanceof ContentObject)
            {
                $this->checkContentObjectForPlagiarism($course, $contentObject, $requestUser);
            }
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     *
     * @throws \Exception
     */
    public function refreshContentObjectPlagiarismChecks(Course $course, User $requestUser)
    {
        $contentObjectIds = [];

        $contentObjectPlagiarismResults = $this->contentObjectPlagiarismResultService->findPlagiarismResults($course);
        foreach ($contentObjectPlagiarismResults as $contentObjectPlagiarismResult)
        {
            $submissionStatus = new SubmissionStatus(
                $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_EXTERNAL_ID],
                $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_STATUS],
                $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_RESULT],
                $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_ERROR]
            );

            if ($submissionStatus->isInProgress() || ($submissionStatus->isFailed() && $submissionStatus->canRetry()))
            {
                $contentObjectIds[] =
                    $contentObjectPlagiarismResult[ContentObjectPlagiarismResult::PROPERTY_CONTENT_OBJECT_ID];
            }
        }

        $this->checkContentObjectsForPlagiarismById($course, $contentObjectIds, $requestUser);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $newStatus
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult|null $contentObjectPlagiarismResult
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     */
    protected function updateResultWithStatus(
        Course $course, ContentObject $contentObject, SubmissionStatus $newStatus,
        ContentObjectPlagiarismResult $contentObjectPlagiarismResult = null, User $requestUser
    )
    {
        if (!empty($contentObjectPlagiarismResult))
        {
            $contentObjectPlagiarismResult->copyFromSubmissionStatus($newStatus);

            $this->contentObjectPlagiarismResultService->updateContentObjectPlagiarismResult(
                $contentObjectPlagiarismResult
            );
        }
        else
        {
            $this->contentObjectPlagiarismResultService->createContentObjectPlagiarismResult(
                $course, $contentObject, $newStatus->getSubmissionId(), $requestUser
            );
        }
    }

    /**
     * @param int $contentObjectPlagiarismResultId
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewUser
     *
     * @return string
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getPlagiarismViewerUrlForContentObjectById(
        int $contentObjectPlagiarismResultId, Course $course, User $viewUser
    )
    {
        $contentObjectPlagiarismResult =
            $this->contentObjectPlagiarismResultService->findPlagiarismResultById($contentObjectPlagiarismResultId);

        if (!$contentObjectPlagiarismResult instanceof ContentObjectPlagiarismResult)
        {
            throw new PlagiarismException(
                sprintf(
                    'The plagiarism result with id %s could not be found', $contentObjectPlagiarismResultId
                )
            );
        }

        if ($contentObjectPlagiarismResult->getCourseId() != $course->getId())
        {
            throw new PlagiarismException(
                sprintf(
                    'The given content object %s is not published in the course %s so the report can not be viewed',
                    $contentObjectPlagiarismResult->getContentObjectId(), $course->getId()
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