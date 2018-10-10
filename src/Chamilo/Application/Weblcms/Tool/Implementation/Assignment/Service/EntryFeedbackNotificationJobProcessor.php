<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Feedback;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Domain\ViewingContext;
use Chamilo\Core\Notification\Service\FilterManager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryFeedbackNotificationJobProcessor extends AssignmentJobProcessor implements JobProcessorInterface
{
    const PARAM_FEEDBACK_ID = 'feedback_id';

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processJob(Job $job)
    {
        $feedbackId = $job->getParameter(self::PARAM_FEEDBACK_ID);
        $feedback = $this->assignmentService->findFeedbackByIdentifier($feedbackId);
        if(!$feedback instanceof Feedback)
        {
            throw new \InvalidArgumentException(
                sprintf('The given feedback with id %s could not be found', $feedbackId)
            );
        }

        $this->processForEntry($feedback->getEntryId());
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

        $feedbacks = $this->assignmentService->findFeedbackByEntry($entry);
        foreach($feedbacks as $feedback)
        {
            $targetUserIds[] = $feedback->get_user_id();
        }

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
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' . $publication->getId() => 'NotificationNewAssignmentFeedbackPublication'
        ];
    }
}