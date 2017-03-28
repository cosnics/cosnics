<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class LearningPathChildAttempt extends \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt
{

    /**
     */
    public function delete()
    {
        $succes = parent::delete();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(), 
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $trackers = DataManager::retrieves(
            LearningPathQuestionAttempt::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($tracker = $trackers->next_result())
        {
            $succes &= $tracker->delete();
        }
        
        return $succes;
    }
}
