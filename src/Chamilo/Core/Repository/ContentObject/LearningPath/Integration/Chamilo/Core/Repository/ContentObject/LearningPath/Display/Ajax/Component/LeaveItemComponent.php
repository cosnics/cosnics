<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\PreviewStorage;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package
 *          core\repository\content_object\learning_path\display\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveItemComponent extends \Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager
{
    const PARAM_TRACKER_ID = 'tracker_id';

    /**
     *
     * @see \libraries\architecture\AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_TRACKER_ID);
    }

    /**
     *
     * @see \libraries\architecture\AjaxManager::run()
     */
    public function run()
    {
        $attempt = PreviewStorage :: getInstance()->retrieve_learning_path_item_attempt(
            $this->getPostDataValue(self :: PARAM_TRACKER_ID));

        if ($attempt instanceof DummyItemAttempt)
        {
            $attempt->set_total_time($attempt->get_total_time() + (time() - $attempt->get_start_time()));

            if ($attempt->update())
            {
                JsonAjaxResult :: success();
            }
            else
            {
                JsonAjaxResult :: bad_request();
            }
        }
        else
        {
            JsonAjaxResult :: bad_request();
        }
    }
}
