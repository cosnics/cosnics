<?php

namespace Chamilo\Application\ExamAssignment\Service;

use Chamilo\Application\ExamAssignment\Domain\AssignmentViewStatus;
use Chamilo\Application\ExamAssignment\Repository\ExamAssignmentRepository;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service\AssignmentPublicationService;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service\UserOvertimeService;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass\UserOvertime;
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
     * @var UserOvertimeService
     */
    protected $userOvertimeService;

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
     * @param UserOvertimeService $userOvertimeService
     */
    public function __construct(
        ExamAssignmentRepository $examAssignmentRepository, CourseService $courseService,
        PublicationService $publicationService, ContentObjectService $contentObjectService,
        WeblcmsRights $weblcmsRights, AssignmentPublicationService $examAssignmentPublicationService,
        AssignmentService $assignmentService, UserService $userService, UserOvertimeService $userOvertimeService
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
        $this->userOvertimeService = $userOvertimeService;
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

        $now = time();

        foreach ($possibleExams as $possibleExam)
        {
            if ($this->userHasRightsOnPublication($user, $possibleExam['publication_id'], $possibleExam['course_id']))
            {
                $userOvertimeData = $this->userOvertimeService->getUserOvertimeData($possibleExam['publication_id'], $user->getId());
                $extraSeconds = $userOvertimeData ? $userOvertimeData->getExtraSeconds() : 0;
                $possibleExam['end_time'] = (string) ($possibleExam['end_time'] + $extraSeconds);
                $possibleExam['has_started'] = $now >= (int) $possibleExam['start_time'];
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
     * @return AssignmentViewStatus
     */
    public function getAssignmentViewStatusForUser(
        User $user, int $contentObjectPublicationId, string $code = null, string $calculatedSecurityCode = null
    )
    {
        $contentObjectPublication = $this->publicationService->getPublication($contentObjectPublicationId);
        if (!$contentObjectPublication instanceof ContentObjectPublication)
        {
            return new AssignmentViewStatus(AssignmentViewStatus::STATUS_CORRUPT_DATA);
        }

        if (!$this->userHasRightsOnPublication($user, $contentObjectPublicationId, $contentObjectPublication->get_course_id()))
        {
            return new AssignmentViewStatus(AssignmentViewStatus::STATUS_NO_RIGHTS);
        }

        $assignment = $this->contentObjectService->findById($contentObjectPublication->get_content_object_id());
        if (!$assignment instanceof Assignment)
        {
            return new AssignmentViewStatus(AssignmentViewStatus::STATUS_CORRUPT_DATA);
        }

        $now = time();

        $userOvertimeData = $this->userOvertimeService->getUserOvertimeData($contentObjectPublication->getId(), $user->getId());
        $extraSeconds = $userOvertimeData ? $userOvertimeData->getExtraSeconds() : 0;

        if ($assignment->get_start_time() > $now || ($assignment->get_end_time() + $extraSeconds + (8 * 3600)) < $now)
        {
            return new AssignmentViewStatus(AssignmentViewStatus::STATUS_ASSIGNMENT_NOT_IN_PROGRESS);
        }

        $examAssignmentPublication =
            $this->examAssignmentPublicationService->getAssignmentPublication($contentObjectPublication);

        if (!$examAssignmentPublication instanceof Publication)
        {
            return new AssignmentViewStatus(AssignmentViewStatus::STATUS_CORRUPT_DATA);
        }

        $examCode = $examAssignmentPublication->getCode();
        $securityCode = $examAssignmentPublication->getSecurityCode($user);

        if (empty($examCode) || $examCode == $code || $calculatedSecurityCode == $securityCode)
        {
            return new AssignmentViewStatus(AssignmentViewStatus::STATUS_ALLOWED);
        }

        return new AssignmentViewStatus(AssignmentViewStatus::STATUS_WRONG_CODE);
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
        return $this->getAssignmentViewStatusForUser($user, $contentObjectPublicationId, $code, $calculatedSecurityCode)
            ->isAllowed();
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
        if (!$this->canUserViewExamAssignment($user, $contentObjectPublicationId, null, $securityCode))
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

        $userOvertimeData = $this->userOvertimeService->getUserOvertimeData($contentObjectPublicationId, $user->getId());
        $extraSeconds = $userOvertimeData ? $userOvertimeData->getExtraSeconds() : 0;

        return $this->isAssignmentEndTimeWithinAcceptableBoundaries($assignment, $extraSeconds);
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
        $details['start_time'] = $assignment->get_start_time();

        $userOvertimeData = $this->userOvertimeService->getUserOvertimeData($contentObjectPublication->getId(), $user->getId());
        $extraSeconds = $userOvertimeData ? $userOvertimeData->getExtraSeconds() : 0;
        $details['end_time'] = (string) ($assignment->get_end_time() + $extraSeconds);

        $details['entries'] = $entries;
        $details['has_finished'] = count($entries) > 0;
        $details['attachments'] = $attachments;
        $details['security_code'] = $examAssignmentPublication->getSecurityCode($user);
        $details['can_submit'] = $this->isAssignmentEndTimeWithinAcceptableBoundaries($assignment, $extraSeconds);

        return $details;
    }

    /**
     * @param Assignment $assignment
     * @param int $userExtraSeconds
     *
     * @return bool
     */
    protected function isAssignmentEndTimeWithinAcceptableBoundaries(Assignment $assignment, int $userExtraSeconds)
    {
        return $assignment->get_end_time() + $userExtraSeconds + 900 >= time();
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
