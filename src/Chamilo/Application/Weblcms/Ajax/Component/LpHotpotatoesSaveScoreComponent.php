<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;

class LpHotpotatoesSaveScoreComponent extends Manager
{

    public function run()
    {
        $id = $this->getRequest()->request->get('id');
        $score = $this->getRequest()->request->get('score');

        $attempt = DataManager::retrieve_by_id(
            LearningPathTreeNodeAttempt::class,
            $id);

        if ($attempt instanceof LearningPathTreeNodeAttempt)
        {
            $attempt->set_score($score)->setCompleted(true)->calculateAndSetTotalTime();

            $attempt->update();
        }

        JsonAjaxResult::success();
    }
}