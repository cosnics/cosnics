<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationProcessor;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryNotificationJobProcessor extends AssignmentJobProcessor implements JobProcessorInterface
{
    const PARAM_ENTRY_ID = 'entry_id';
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
        $entryId = $job->getParameter(self::PARAM_ENTRY_ID);
        $contentObjectPublicationId = $job->getParameter(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);
        $this->processForEntry($entryId, $contentObjectPublicationId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry $entry
     *
     * @return int
     */
    protected function getCreationDate(Entry $entry)
    {
        return $entry->getSubmitted();
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
            'Chamilo' => 'NotificationNewAssignmentEntry',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentEntryCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' . $publication->getId() => 'NotificationNewAssignmentEntryPublication',
            'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\LearningPath:' . $publication->getId() . '::TreeNodeData:' . $treeNodeData->getId() => 'NotificationNewAssignmentEntryAssignmentTreeNode'
        ];
    }
}