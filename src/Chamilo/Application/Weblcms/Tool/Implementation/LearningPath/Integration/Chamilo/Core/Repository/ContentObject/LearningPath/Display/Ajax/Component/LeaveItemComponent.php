<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Exception;

/**
 * @package
 *          core\repository\content_object\learning_path\display\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LeaveItemComponent extends Manager
{
    public const PARAM_TRACKER_ID = 'tracker_id';

    public function run()
    {
        try
        {
            $treeNodeAttemptId = $this->getPostDataValue(self::PARAM_TRACKER_ID);
            $trackingService = $this->buildTrackingService();

            $trackingService->setAttemptTotalTimeByTreeNodeAttemptId($treeNodeAttemptId);
        }
        catch (Exception $ex)
        {
            JsonAjaxResult::bad_request();
        }

        JsonAjaxResult::success();
    }

    /**
     * Builds the TrackingService
     *
     * @return TrackingService
     */
    public function buildTrackingService()
    {
        $trackingServiceBuilder = $this->getTrackingServiceBuilder();

        return $trackingServiceBuilder->buildTrackingService(
            new TrackingParameters(1)
        );
    }

    /**
     * @return array
     */
    public function getRequiredPostParameters(): array
    {
        return [self::PARAM_TRACKER_ID];
    }

    /**
     * @return TrackingServiceBuilder | object
     */
    protected function getTrackingServiceBuilder()
    {
        return new TrackingServiceBuilder(
            $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository')
        );
    }
}
