<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class HotpotatoesSaveScoreComponent extends Manager
{

    public function run()
    {
        $id = Request::post('id');
        $score = Request::post('score');
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                AssessmentAttempt::class_name(),
                AssessmentAttempt::PROPERTY_ID),
            new StaticConditionVariable($id));
        
        $tracker = DataManager::retrieve(
            AssessmentAttempt::class_name(),
            new DataClassRetrieveParameters($condition));
        
        if ($tracker)
        {
            $end_time = time();
            
            $tracker->set_total_score($score);
            $tracker->set_status(AssessmentAttempt::STATUS_COMPLETED);
            $tracker->set_end_time($end_time);
            $tracker->set_total_time($tracker->get_total_time() + ($end_time - $tracker->get_start_time()));
            
            $tracker->update();
        }
        
        JsonAjaxResult::success();
    }
}