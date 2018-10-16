<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository;


/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackRepository  extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository
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
    protected function getFeedbackClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback::class;
    }
}