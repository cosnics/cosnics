<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository;

/**
 * Manages the entities for the learning path assignment submissions
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentRepository extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\AssignmentRepository
{
    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry::class;
    }

    /**
     * @return string
     */
    protected function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score::class;
    }

    /**
     * @return string
     */
    protected function getEntryAttachmentClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\EntryAttachment::class;
    }
}