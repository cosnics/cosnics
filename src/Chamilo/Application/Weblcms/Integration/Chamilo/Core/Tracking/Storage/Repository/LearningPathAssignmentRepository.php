<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository;

/**
 * Manages the entities for the learning path assignment submissions
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathAssignmentRepository extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathAssignmentRepository
{
    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry::class;
    }

    /**
     * @return string
     */
    protected function getFeedbackClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback::class;
    }

    /**
     * @return string
     */
    protected function getNoteClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note::class;
    }

    /**
     * @return string
     */
    protected function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score::class;
    }

    /**
     * @return string
     */
    protected function getEntryAttachmentClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\EntryAttachment::class;
    }
}