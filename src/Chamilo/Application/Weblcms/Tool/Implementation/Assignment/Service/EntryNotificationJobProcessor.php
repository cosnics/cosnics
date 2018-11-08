<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryNotificationJobProcessor extends AssignmentJobProcessor implements JobProcessorInterface
{
    const PARAM_ENTRY_ID = 'entry_id';

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processJob(Job $job)
    {
        $entryId = $job->getParameter(self::PARAM_ENTRY_ID);
        $this->processForEntry($entryId);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry $entry
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
     *
     * @return array
     */
    protected function getNotificationViewingContextVariables(Course $course, ContentObjectPublication $publication)
    {
        return [
            'Chamilo' => 'NotificationNewAssignmentEntry',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentEntryCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' . $publication->getId() => 'NotificationNewAssignmentEntryPublication'
        ];
    }
}