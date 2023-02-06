<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ScoreDataService
{
    /**
     * @param ContentObjectPublication $publication
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\RecordResultSet
     */
    public function getAssessmentAttempts(ContentObjectPublication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($publication->getId())
        );
        return WeblcmsTrackingDataManager::retrieve_assessment_attempts_with_user($condition);
    }
}