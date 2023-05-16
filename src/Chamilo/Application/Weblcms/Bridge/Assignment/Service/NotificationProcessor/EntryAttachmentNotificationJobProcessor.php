<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationProcessor;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\EntryAttachment;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryAttachmentNotificationJobProcessor extends AssignmentJobProcessor implements JobProcessorInterface
{
    const PARAM_ENTRY_ATTACHMENT_ID = 'entry_attachment_id';
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
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     */
    public function processJob(Job $job)
    {
        $this->job = $job;

        $entryAttachmentId = $job->getParameter(self::PARAM_ENTRY_ATTACHMENT_ID);
        $entryAttachment = $this->assignmentService->findEntryAttachmentById($entryAttachmentId);
        if (!$entryAttachment instanceof EntryAttachment)
        {
            throw new JobNoLongerValidException(
                sprintf('The given entryAttachment with id %s could not be found', $entryAttachmentId)
            );
        }

        $this->entryAttachment = $entryAttachment;

        $this->processForEntry($entryAttachment->getEntryId());
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getCreationDate(Entry $entry)
    {
        return $this->entryAttachment->getCreated();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
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
     * @param Entry $entry
     *
     * @return string
     */
    protected function getNotificationUrl($course, $publication, $entry): string
    {
        $notificationUrl = parent::getNotificationUrl($course, $publication, $entry);
        $notificationUrl .= '#entryAttachments';

        return $notificationUrl;
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
            'Chamilo' => 'NotificationNewAssignmentEntryAttachment',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentEntryAttachmentCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' .
            $publication->getId() => 'NotificationNewAssignmentEntryAttachmentPublication'
        ];
    }
}