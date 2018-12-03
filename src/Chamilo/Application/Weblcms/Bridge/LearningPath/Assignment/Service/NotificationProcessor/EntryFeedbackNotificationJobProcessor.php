<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationProcessor;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\FeedbackService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Notification\Service\FilterManager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
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
    const PARAM_CONTENT_OBJECT_PUBLICATION_ID = 'content_object_publication_id';

    /**
     * @var Feedback
     */
    protected $feedback;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\FeedbackService
     */
    protected $feedbackService;

    /**
     * EntryNotificationProcessor constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\FeedbackService $feedbackService
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService $assignmentService
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService $treeNodeDataService
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param FilterManager $filterManager
     * @param NotificationManager $notificationManager
     */
    public function __construct(
        FeedbackService $feedbackService, AssignmentService $assignmentService, TreeNodeDataService $treeNodeDataService,
        PublicationService $publicationService, CourseService $courseService,
        UserService $userService, ContentObjectRepository $contentObjectRepository,
        FilterManager $filterManager, NotificationManager $notificationManager
    )
    {
        parent::__construct(
            $assignmentService, $treeNodeDataService, $publicationService, $courseService, $userService,
            $contentObjectRepository, $filterManager, $notificationManager
        );

        $this->feedbackService = $feedbackService;
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     */
    public function processJob(Job $job)
    {
        $feedbackId = $job->getParameter(self::PARAM_FEEDBACK_ID);
        $contentObjectPublicationId = $job->getParameter(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);

        $feedback = $this->feedbackService->findFeedbackByIdentifier($feedbackId);
        if (!$feedback instanceof Feedback)
        {
            throw new JobNoLongerValidException(
                sprintf('The given feedback with id %s could not be found', $feedbackId)
            );
        }

        $this->feedback = $feedback;

        $this->processForEntry($feedback->getEntryId(), $contentObjectPublicationId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getCreationDate(Entry $entry)
    {
        return $this->feedback->get_creation_date();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
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
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param Entry $entry
     *
     * @return string
     */
    protected function getNotificationUrl(
        Course $course, ContentObjectPublication $publication, TreeNodeData $treeNodeData, Entry $entry
    )
    {
        $notificationUrl = parent::getNotificationUrl($course, $publication, $treeNodeData, $entry);
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

        return array_unique($targetUserIds);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $publication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return array
     */
    protected function getNotificationViewingContextVariables(
        Course $course, ContentObjectPublication $publication, TreeNodeData $treeNodeData
    )
    {
        return [
            'Chamilo' => 'NotificationNewAssignmentFeedback',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentFeedbackCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' .
            $publication->getId() => 'NotificationNewAssignmentFeedbackPublication',
            'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\LearningPath:' . $publication->getId() .
            '::TreeNodeData:' . $treeNodeData->getId() => 'NotificationNewAssignmentFeedbackAssignmentTreeNode'
        ];
    }
}