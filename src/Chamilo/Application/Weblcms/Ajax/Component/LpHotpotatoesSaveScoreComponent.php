<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;

class LpHotpotatoesSaveScoreComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{

    public function run()
    {
        $id = Request::post('id');
        $score = Request::post('score');
        
        $attempt = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
            LearningPathItemAttempt::class_name(), 
            $id);
        
        if ($attempt instanceof LearningPathItemAttempt)
        {
            $attempt->set_score($score);
            $attempt->set_status('completed');
            $attempt->set_total_time($attempt->get_total_time() + (time() - $attempt->get_start_time()));
            $attempt->update();
        }
        
        JsonAjaxResult::success();
    }
}