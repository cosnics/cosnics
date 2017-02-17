<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package
 *          core\repository\content_object\learning_path\display\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveItemComponent extends \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager
{
    const PARAM_TRACKER_ID = 'tracker_id';

    /**
     *
     * @see \libraries\architecture\AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_TRACKER_ID);
    }

    /**
     *
     * @see \libraries\architecture\AjaxManager::run()
     */
    public function run()
    {
        $attempt = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
            LearningPathItemAttempt::class_name(),
            $this->getPostDataValue(self::PARAM_TRACKER_ID));

        if ($attempt instanceof LearningPathItemAttempt)
        {
            $attempt->set_total_time($attempt->get_total_time() + (time() - $attempt->get_start_time()));

            if ($attempt->update())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::bad_request();
            }
        }
        else
        {
            JsonAjaxResult::bad_request();
        }
    }
}
