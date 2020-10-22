<?php

namespace Chamilo\Application\ExamAssignment\Service;

use Chamilo\Application\ExamAssignment\Repository\ExamAssignmentRepository;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
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
     * @var WeblcmsRights
     */
    protected $weblcmsRights;

    /**
     * ExamAssignmentService constructor.
     *
     * @param ExamAssignmentRepository $examAssignmentRepository
     * @param CourseService $courseService
     * @param WeblcmsRights $weblcmsRights
     */
    public function __construct(
        ExamAssignmentRepository $examAssignmentRepository, CourseService $courseService, WeblcmsRights $weblcmsRights
    )
    {
        $this->examAssignmentRepository = $examAssignmentRepository;
        $this->courseService = $courseService;
        $this->weblcmsRights = $weblcmsRights;
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
            if ($this->weblcmsRights->is_allowed_in_courses_subtree(
                WeblcmsRights::VIEW_RIGHT, $possibleExam['publication_id'], WeblcmsRights::TYPE_PUBLICATION,
                $possibleExam['course_id'], $user->getId()
            ))
            {
                $userExams[] = $possibleExam;
            }
        }

        return $userExams;
    }

}
