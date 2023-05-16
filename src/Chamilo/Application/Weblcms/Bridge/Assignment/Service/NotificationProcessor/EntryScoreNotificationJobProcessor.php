<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationProcessor;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Score;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Queue\Storage\Entity\Job;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryScoreNotificationJobProcessor extends AssignmentJobProcessor implements JobProcessorInterface
{
    const PARAM_SCORE_ID = 'score_id';

    /**
     * @var Score
     */
    protected $score;

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     */
    public function processJob(Job $job)
    {
        $scoreId = $job->getParameter(self::PARAM_SCORE_ID);
        $score = $this->assignmentService->findScoreByIdentifier($scoreId);

        if (!$score instanceof Score)
        {
            throw new JobNoLongerValidException(
                sprintf('The given score with id %s could not be found', $scoreId)
            );
        }

        $this->score = $score;

        $this->processForEntry($score->getEntryId());
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getCreationDate(Entry $entry)
    {
        return $this->score->getModified();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    protected function getUserId(Entry $entry)
    {
        return $this->score->getUserId();
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
        $notificationUrl .= '#score';

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
            'Chamilo' => 'NotificationNewAssignmentScore',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId() => 'NotificationNewAssignmentScoreCourse',
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' .
            $publication->getId() => 'NotificationNewAssignmentScorePublication'
        ];
    }
}