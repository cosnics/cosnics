<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationProcessor;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryAttachmentNotificationJobProcessor extends AssignmentJobProcessor implements JobProcessorInterface
{
    const PARAM_ENTRY_ATTACHMENT_ID = 'entry_attachment_id';
    const PARAM_CONTENT_OBJECT_PUBLICATION_ID = 'content_object_publication_id';
    const PARAM_USER_ID = 'user_id';

    /**
     * @var EntryAttachment
     */
    protected $entryAttachment;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    public function processJob(Job $job)
    {
        $this->job = $job;

        $entryAttachmentId = $job->getParameter(self::PARAM_ENTRY_ATTACHMENT_ID);
        $contentObjectPublicationId = $job->getParameter(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);

        $entryAttachment = $this->assignmentService->findEntryAttachmentById($entryAttachmentId);
        if (!$entryAttachment instanceof EntryAttachment)
        {
            throw new \InvalidArgumentException(
                sprintf('The given entryAttachment with id %s could not be found', $entryAttachmentId)
            );
        }

        $this->entryAttachment = $entryAttachment;

        $this->processForEntry($entryAttachment->getEntryId(), $contentObjectPublicationId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getCreationDate(Entry $entry)
    {
        return $this->entryAttachment->getCreated();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getUserId(Entry $entry)
    {
        return $this->job->getParameter(self::PARAM_USER_ID);
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
        $notificationUrl .= '#entryAttachment' . $this->entryAttachment->getId();

        return $notificationUrl;
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
            'Chamilo' => 'NotificationNewAssignmentEntryAttachment',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentEntryAttachmentCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' .
            $publication->getId() => 'NotificationNewAssignmentEntryAttachmentPublication',
            'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\LearningPath:' . $publication->getId() .
            '::TreeNodeData:' . $treeNodeData->getId() => 'NotificationNewAssignmentEntryAttachmentAssignmentTreeNode'
        ];
    }
}