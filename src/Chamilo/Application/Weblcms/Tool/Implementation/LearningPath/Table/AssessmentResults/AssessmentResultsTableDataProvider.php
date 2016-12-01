<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table\AssessmentResults;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This component provides the data for the table.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssessmentResultsTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if ($order_property == null)
        {
            $order_property = new OrderBy(
                new PropertyConditionVariable(
                    LearningPathItemAttempt::class_name(), 
                    LearningPathItemAttempt::PROPERTY_START_TIME));
        }
        
        return DataManager::retrieves(
            LearningPathItemAttempt::class_name(), 
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public function count_data($condition)
    {
        return DataManager::count(LearningPathItemAttempt::class_name(), new DataClassCountParameters($condition));
    }
}
