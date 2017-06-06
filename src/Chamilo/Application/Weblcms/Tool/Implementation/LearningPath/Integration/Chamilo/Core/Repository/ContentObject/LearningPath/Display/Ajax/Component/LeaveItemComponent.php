<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\TreeNodeDataAttempt;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\LearningPathTrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingServiceBuilder;
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
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_TRACKER_ID);
    }

    public function run()
    {
        try
        {
            $treeNodeDataAttemptId = $this->getPostDataValue(self::PARAM_TRACKER_ID);
            $learningPathTrackingService = $this->buildLearningPathTrackingService();

            $learningPathTrackingService->setAttemptTotalTimeByTreeNodeDataAttemptId($treeNodeDataAttemptId);
        }
        catch(\Exception $ex)
        {
            JsonAjaxResult::bad_request();
        }

        JsonAjaxResult::success();
    }

    /**
     * Builds the LearningPathTrackingService
     *
     * @return LearningPathTrackingService
     */
    public function buildLearningPathTrackingService()
    {
        $learningPathTrackingServiceBuilder = $this->getLearningPathTrackingServiceBuilder();

        return $learningPathTrackingServiceBuilder->buildLearningPathTrackingService(
            new LearningPathTrackingParameters(1, 1)
        );
    }

    /**
     * @return LearningPathTrackingServiceBuilder | object
     */
    protected function getLearningPathTrackingServiceBuilder()
    {
        return new LearningPathTrackingServiceBuilder(
            $this->getService('chamilo.libraries.storage.data_manager.doctrine.data_class_repository')
        );
    }
}
