<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;

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
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    public function processJob(Job $job)
    {
        $feedbackId = $job->getParameter(self::PARAM_FEEDBACK_ID);
        $contentObjectPublicationId = $job->getParameter(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);

        $feedback = $this->assignmentService->findFeedbackByIdentifier($feedbackId);
        if(!$feedback instanceof Feedback)
        {
            throw new \InvalidArgumentException(
                sprintf('The given feedback with id %s could not be found', $feedbackId)
            );
        }

        $this->processForEntry($feedback->getEntryId(), $contentObjectPublicationId);
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
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return array
     */
    protected function getNotificationViewingContextVariables(Course $course, ContentObjectPublication $publication, TreeNodeData $treeNodeData)
    {
        return [
            'Chamilo' => 'NotificationNewAssignmentFeedback',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentFeedbackCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' . $publication->getId() => 'NotificationNewAssignmentFeedbackPublication',
            'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\LearningPath::TreeNodeData:' . $treeNodeData->getId() => 'NotificationNewAssignmentFeedbackAssignmentTreeNode'
        ];
    }
}