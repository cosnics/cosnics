<?php

namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathChildAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;

class LpHotpotatoesSaveScoreComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{

    public function run()
    {
        $id = Request::post('id');
        $score = Request::post('score');

        $attempt = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
            LearningPathTreeNodeAttempt::class_name(),
            $id
        );

        if ($attempt instanceof LearningPathTreeNodeAttempt)
        {
            $attempt->set_score($score)
                ->setCompleted(true)
                ->calculateAndSetTotalTime();

            $attempt->update();
        }

        JsonAjaxResult::success();
    }
}