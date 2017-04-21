<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\LearningPathTrackingParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Extension on the Weblcms LearningPathTrackingParameters for data migration
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathMigrationTrackingParameters extends LearningPathTrackingParameters
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {

    }

    /**
     * @return Condition
     */
    public function getLearningPathAttemptConditions()
    {
        return null;
    }
}