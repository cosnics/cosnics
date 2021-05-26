<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AsessmentAttempt;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * This class represents a data provider for the attempts of an assessment
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentAttemptTableDataProvider extends RecordTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return WeblcmsTrackingDataManager::retrieve_assessment_attempts_with_user(
            $condition, 
            $offset, 
            $count, 
            $order_property);
    }

    /**
     * Counts the data
     * 
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return WeblcmsTrackingDataManager::count_assessment_attempts_with_user($condition);
    }
}
