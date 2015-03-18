<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class HotpotatoesSaveScoreComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{

    public function run()
    {
        $id = Request :: post('id');
        $score = Request :: post('score');

        $dummy = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: PROPERTY_ID),
            new StaticConditionVariable($id));

        $trackers = $dummy->retrieve_tracker_items($condition);

        if ($trackers[0])
        {
            $end_time = time();

            $trackers[0]->set_total_score($score);
            $trackers[0]->set_status(AssessmentAttempt :: STATUS_COMPLETED);
            $trackers[0]->set_end_time($end_time);
            $trackers[0]->set_total_time($trackers[0]->get_total_time() + ($end_time - $trackers[0]->get_start_time()));
            $trackers[0]->update();
        }

        JsonAjaxResult :: success();
    }
}