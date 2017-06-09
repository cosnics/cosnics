<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Extension on the Weblcms TrackingParameters for data migration
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MigrationTrackingParameters extends TrackingParameters
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {

    }

    /**
     * @return Condition
     */
    public function getTreeNodeAttemptConditions()
    {
        return null;
    }
}