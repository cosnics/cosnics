<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusRepository extends \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\EphorusRepository
{
    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return Entry::class;
    }

}