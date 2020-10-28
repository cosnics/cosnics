<?php

namespace Chamilo\Application\ExamAssignment\Service;

use Chamilo\Application\ExamAssignment\Repository\ExamAssignmentRepository;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service\AssignmentPublicationService;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class ExamAssignmentService
 * @package Chamilo\Application\ExamAssignment\Service
 */
class ExamAssignmentService
{
    /**
     * @var ExamAssignmentRepository
     */
    protected $examAssignmentRepository;

    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var AssignmentPublicationService
     */
    protected $examAssignmentPublicationService;

    /**
     * @var ContentObjectService
     */
    protected $contentObjectService;

    /**
     * @var WeblcmsRights
     */
    protected $weblcmsRights;

    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * ExamAssignmentService constructor.
     *
     * @param ExamAssignmentRepository $examAssignmentRepository
     * @param CourseService $courseService
     * @param PublicationService $publicationService
     * @param ContentObjectService $contentObjectService
     * @param WeblcmsRights $weblcmsRights
     * @param AssignmentPublicationService $examAssignmentPublicationService
     * @param AssignmentService $assignmentService
     * @param UserService $userService
     */
    public function __construct(
        ExamAssignmentRepository $examAssignmentRepository, CourseService $courseService,
        PublicationService $publicationService, ContentObjectService $contentObjectService,
        WeblcmsRights $weblcmsRights, AssignmentPublicationService $examAssignmentPublicationService,
        AssignmentService $assignmentService, UserService $userService
    )
    {
        $this->examAssignmentRepository = $examAssignmentRepository;
        $this->courseService = $courseService;
        $this->publicationService = $publicationService;
        $this->contentObjectService = $contentObjectService;
        $this->weblcmsRights = $weblcmsRights;
        $this->examAssignmentPublicationService = $examAssignmentPublicationService;
        $this->assignmentService = $assignmentService;
        $this->userService = $userService;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getCurrentExamAssignmentsForUser(User $user)
    {
        $courseIds = [];

        $courses = $this->courseService->getAllCoursesForUser($user);
        foreach ($courses as $course)
        {
            $courseIds[] = $course->getId();
        }

        $possibleExams = $this->examAssignmentRepository->getCurrentExamAssignmentsInCourses($courseIds);

        $userExams = [];

        foreach ($possibleExams as $possibleExam)
        {
            if ($this->userHasRightsOnPublication($user, $possibleExam['publication_id'], $possibleExam['course_id']))
            {
                $userExams[] = $possibleExam;
            }
        }

        return $userExams;
    }

    /**
     * @param User $user
     * @param int $contentObjectPublicationId
     * @param string|null $code
     * @param string|null $calculatedSecurityCode
     *
     * @return bool
     */
    public function canUserViewExamAssignment(
        User $user, int $contentObjectPublicationId, string $code = null, string $calculatedSecurityCode = null
    )
    {
        $contentObjectPublication = $this->publicationService->getPublication($contentObjectPublicationId);
        if (!$contentObjectPublication instanceof ContentObjectPublication)
        {
            return false;
        }

        if (
        !$this->userHasRightsOnPublication(
            $user, $contentObjectPublicationId, $contentObjectPublication->get_course_id()
        )
        )
        {
            return false;
        }

        $assignment = $this->contentObjectService->findById($contentObjectPublication->get_content_object_id());
        if (!$assignment instanceof Assignment)
        {
            return false;
        }

        $now = time();

        if ($assignment->get_start_time() > $now || ($assignment->get_end_time() + (8 * 3600)) < $now)
        {
            return false;
        }

        $examAssignmentPublication =
            $this->examAssignmentPublicationService->getAssignmentPublication($contentObjectPublication);

        if (!$examAssignmentPublication instanceof Publication)
        {
            return false;
        }

        $examCode = $examAssignmentPublication->getCode();
        $securityCode = $examAssignmentPublication->getSecurityCode($user);

        if (empty($examCode) || $examCode == $code || $calculatedSecurityCode == $securityCode)
        {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param int $contentObjectPublicationId
     * @param string $securityCode
     *
     * @return bool
     */
    public function canUserSubmit(User $user, int $contentObjectPublicationId, string $securityCode)
    {
        if(!$this->canUserViewExamAssignment($user, $contentObjectPublicationId, null, $securityCode))
        {
            return false;
        }

        $contentObjectPublication = $this->publicationService->getPublication($contentObjectPublicationId);
        if (!$contentObjectPublication instanceof ContentObjectPublication)
        {
            return false;
        }

        $assignment = $this->contentObjectService->findById($contentObjectPublication->get_content_object_id());
        if (!$assignment instanceof Assignment)
        {
            return false;
        }

        return $this->isAssignmentEndTimeWithinAcceptableBoundaries($assignment);
    }

    /**
     * @param User $user
     * @param int $contentObjectPublicationId
     *
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getExamAssignmentDetails(User $user, int $contentObjectPublicationId)
    {
        $contentObjectPublication = $this->publicationService->getPublication($contentObjectPublicationId);
        $course = $this->courseService->getCourseById($contentObjectPublication->get_course_id());
        $titular = $this->userService->findUserByIdentifier($course->get_titular_id());

        /** @var Assignment $assignment */
        $assignment = $this->contentObjectService->findById($contentObjectPublication->get_content_object_id());
        $entries = $this->assignmentService->findEntriesForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication, Entry::ENTITY_TYPE_USER, $user->getId()
        )->getArrayCopy();

        $attachments = [];

        foreach ($assignment->get_attachments() as $attachment)
        {
            if ($attachment instanceof File)
            {
                $attachments[$attachment->get_filename()] =
                    \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                        $attachment->getId(), $attachment->calculate_security_code()
                    );
            }
        }

        $examAssignmentPublication =
            $this->examAssignmentPublicationService->getAssignmentPublication($contentObjectPublication);

        $details = [];

        $details['publication'] = $contentObjectPublication;
        $details['course'] = $course;
        $details['titular'] = $titular;
        $details['assignment'] = $assignment;
        $details['entries'] = $entries;
        $details['has_finished'] = count($entries) > 0;
        $details['attachments'] = $attachments;
        $details['security_code'] = $examAssignmentPublication->getSecurityCode($user);
        $details['can_submit'] = $this->isAssignmentEndTimeWithinAcceptableBoundaries($assignment);

        return $details;
    }

    /**
     * @param Assignment $assignment
     *
     * @return bool
     */
    protected function isAssignmentEndTimeWithinAcceptableBoundaries(Assignment $assignment)
    {
        return $assignment->get_end_time() + 900 >= time();
    }

    /**
     * @param User $user
     * @param int $publicationId
     * @param int $courseId
     *
     * @return bool
     */
    protected function userHasRightsOnPublication(User $user, int $publicationId, int $courseId)
    {
        return $this->weblcmsRights->is_allowed_in_courses_subtree(
            WeblcmsRights::VIEW_RIGHT, $publicationId, WeblcmsRights::TYPE_PUBLICATION, $courseId, $user->getId()
        );
    }

}
