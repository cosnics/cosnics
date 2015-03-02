<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveItemComponent extends \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Ajax\Manager
{

    public function run()
    {
        $tracker_id = Request :: post('tracker_id');

        $attempt = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieve_by_id(
            LearningPathItemAttempt :: class_name(),
            $tracker_id);
        $attempt->set_total_time($attempt->get_total_time() + (time() - $attempt->get_start_time()));
        $attempt->update();
        JsonAjaxResult :: success();
    }
}