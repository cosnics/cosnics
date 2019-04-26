<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;

/**
 * @package Chamilo\Application\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPlagiarismResultService
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\Repository\ContentObjectPlagiarismResultRepository
     */
    protected $contentObjectPlagiarismResultRepository;

    /**
     * ContentObjectPlagiarismResultService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\Repository\ContentObjectPlagiarismResultRepository $contentObjectPlagiarismResultRepository
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\Repository\ContentObjectPlagiarismResultRepository $contentObjectPlagiarismResultRepository
    )
    {
        $this->contentObjectPlagiarismResultRepository = $contentObjectPlagiarismResultRepository;
    }

    /**
     * @param int $plagiarismResultId
     *
     * @return ContentObjectPlagiarismResult
     */
    public function findPlagiarismResultById(int $plagiarismResultId)
    {
        return $this->contentObjectPlagiarismResultRepository->findPlagiarismResultById($plagiarismResultId);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlagiarismResultByContentObject(Course $course, ContentObject $contentObject)
    {
        return $this->contentObjectPlagiarismResultRepository->findPlagiarismResultByContentObject(
            $course, $contentObject
        );
    }

    /**
     * @param string $externalId
     *
     * @return ContentObjectPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlagiarismResultByExternalId(string $externalId)
    {
        return $this->contentObjectPlagiarismResultRepository->findPlagiarismResultByExternalId($externalId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countPlagiarismResults(Course $course, FilterParameters $filterParameters)
    {
        return $this->contentObjectPlagiarismResultRepository->countPlagiarismResults($course, $filterParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findPlagiarismResults(Course $course, FilterParameters $filterParameters = null)
    {
        return $this->contentObjectPlagiarismResultRepository->findPlagiarismResults($course, $filterParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $externalId
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $requestUser
     *
     * @return ContentObjectPlagiarismResult
     */
    public function createContentObjectPlagiarismResult(
        Course $course, ContentObject $contentObject, string $externalId, User $requestUser
    )
    {
        $contentObjectPlagiarismResult = new ContentObjectPlagiarismResult();

        $contentObjectPlagiarismResult->setContentObjectId($contentObject->getId());
        $contentObjectPlagiarismResult->setCourseId($course->getId());
        $contentObjectPlagiarismResult->setExternalId($externalId);
        $contentObjectPlagiarismResult->setStatus(SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS);
        $contentObjectPlagiarismResult->setRequestUserId($requestUser->getId());
        $contentObjectPlagiarismResult->setRequestDate(new \DateTime());

        if (!$this->contentObjectPlagiarismResultRepository->createPlagiarismResult($contentObjectPlagiarismResult))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The content object plagiarism result for content object %s could not be created',
                    $contentObject->getId()
                )
            );
        }

        return $contentObjectPlagiarismResult;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult $contentObjectPlagiarismResult
     *
     * @return ContentObjectPlagiarismResult
     */
    public function updateContentObjectPlagiarismResult(ContentObjectPlagiarismResult $contentObjectPlagiarismResult)
    {
        if (!$this->contentObjectPlagiarismResultRepository->updatePlagiarismResult($contentObjectPlagiarismResult))
        {
            throw new \RuntimeException(
                sprintf('The entry plagiarism result %s could not be update', $contentObjectPlagiarismResult->getId())
            );
        }

        return $contentObjectPlagiarismResult;
    }

}