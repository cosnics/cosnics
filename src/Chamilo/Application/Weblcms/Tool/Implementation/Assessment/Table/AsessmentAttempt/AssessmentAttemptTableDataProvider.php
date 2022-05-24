<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AsessmentAttempt;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * This class represents a data provider for the attempts of an assessment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentAttemptTableDataProvider extends RecordTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return WeblcmsTrackingDataManager::count_assessment_attempts_with_user($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return WeblcmsTrackingDataManager::retrieve_assessment_attempts_with_user(
            $condition, $offset, $count, $orderBy
        );
    }
}
