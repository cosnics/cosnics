<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationProcessor;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\FeedbackService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Feedback;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Core\Notification\Service\FilterManager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryFeedbackNotificationJobProcessor extends AssignmentJobProcessor implements JobProcessorInterface
{
    const PARAM_FEEDBACK_ID = 'feedback_id';

    /**
     * @var Feedback
     */
    protected $feedback;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\FeedbackService
     */
    protected $feedbackService;

    /**
     * EntryNotificationProcessor constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\FeedbackService $feedbackService
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param FilterManager $filterManager
     * @param NotificationManager $notificationManager
     */
    public function __construct(
        FeedbackService $feedbackService, AssignmentService $assignmentService,
        EntityServiceManager $entityServiceManager,
        PublicationService $publicationService, CourseService $courseService,
        UserService $userService, ContentObjectRepository $contentObjectRepository,
        FilterManager $filterManager, NotificationManager $notificationManager
    )
    {
        parent::__construct(
            $assignmentService, $entityServiceManager, $publicationService, $courseService, $userService,
            $contentObjectRepository, $filterManager, $notificationManager
        );

        $this->feedbackService = $feedbackService;
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     */
    public function processJob(Job $job)
    {
        $feedbackId = $job->getParameter(self::PARAM_FEEDBACK_ID);
        $feedback = $this->feedbackService->findFeedbackByIdentifier($feedbackId);
        if (!$feedback instanceof Feedback)
        {
            throw new JobNoLongerValidException(
                sprintf('The given feedback with id %s could not be found', $feedbackId)
            );
        }

        $this->feedback = $feedback;

        $this->processForEntry($feedback->getEntryId());
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getCreationDate(Entry $entry)
    {
        return $this->feedback->get_creation_date();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getUserId(Entry $entry)
    {
        return $this->feedback->get_user_id();
    }

    /**
     * @param Course $course
     * @param ContentObjectPublication $publication
     * @param Entry $entry
     *
     * @return string
     */
    protected function getNotificationUrl($course, $publication, $entry): string
    {
        $notificationUrl = parent::getNotificationUrl($course, $publication, $entry);
        $notificationUrl .= '#feedback' . $this->feedback->getId();

        return $notificationUrl;
    }

    /**
     * @param Course $course
     * @param Entry $entry
     *
     * @return int[]
     */
    protected function getTargetUserIds($course, $entry)
    {
        $targetUserIds = parent::getTargetUserIds($course, $entry);

        $feedbacks = $this->feedbackService->findFeedbackByEntry($entry);
        foreach ($feedbacks as $feedback)
        {
            $targetUserIds[] = $feedback->get_user_id();
        }

        $targetUserIds = $this->filterTargetUsers($entry, $targetUserIds);

        return array_unique($targetUserIds);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $publication
     *
     * @return array
     */
    protected function getNotificationViewingContextVariables(Course $course, ContentObjectPublication $publication)
    {
        return [
            'Chamilo' => 'NotificationNewAssignmentFeedback',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentFeedbackCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' .
            $publication->getId() => 'NotificationNewAssignmentFeedbackPublication'
        ];
    }
}