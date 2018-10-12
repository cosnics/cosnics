<?php
namespace Chamilo\Core\Repository\Feedback;

use Chamilo\Core\Repository\Feedback\Bridge\FeedbackBridgeAdapter;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackBridgeInterface;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsBridgeAdapter;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsBridgeInterface;
use Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationService;
use Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationServiceInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

abstract class Manager extends Application
{
    const PARAM_FEEDBACK_BRIDGE = 'FeedbackBridge';
    const PARAM_FEEDBACK_RIGHTS_BRIDGE = 'FeedbackRightsBridge';

    // Parameters
    const PARAM_ACTION = 'feedback_action';
    const PARAM_FEEDBACK_ID = 'feedback_id';
    
    // Actions
    const ACTION_BROWSE_V2 = 'BrowserV2';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_SUBSCRIBER = 'Subscriber';
    const ACTION_UNSUBSCRIBER = 'Unsubscriber';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const CONFIGURATION_SHOW_FEEDBACK_HEADER = 'showFeedbackHeader';

    /**
     * @var \Chamilo\Core\Repository\Feedback\Bridge\FeedbackBridgeInterface
     */
    protected $feedbackBridge;

    /**
     * @var \Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsBridgeInterface
     */
    protected $feedbackRightsBridge;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if(!$this->get_application() instanceof \Chamilo\Core\Repository\Feedback\FeedbackSupport) {
            throw new NotAllowedException();
        }

        $this->initializeBridges($applicationConfiguration);
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    protected function initializeBridges(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if($applicationConfiguration->get(self::PARAM_FEEDBACK_BRIDGE) instanceof FeedbackBridgeInterface)
        {
            $this->feedbackBridge = $applicationConfiguration->get(self::PARAM_FEEDBACK_BRIDGE);
        }
        else
        {
            /** @var \Chamilo\Core\Repository\Feedback\FeedbackSupport $application */
            $application = $this->get_application();
            $this->feedbackBridge = new FeedbackBridgeAdapter($application);
        }

        if($applicationConfiguration->get(self::PARAM_FEEDBACK_RIGHTS_BRIDGE) instanceof FeedbackRightsBridgeInterface)
        {
            $this->feedbackRightsBridge = $applicationConfiguration->get(self::PARAM_FEEDBACK_RIGHTS_BRIDGE);
        }
        else
        {
            /** @var \Chamilo\Core\Repository\Feedback\FeedbackSupport $application */
            $application = $this->get_application();
            $this->feedbackRightsBridge = new FeedbackRightsBridgeAdapter($application);
        }
    }

    /**
     * Returns the notification service
     * 
     * @return NotificationServiceInterface
     */
    public function getNotificationService()
    {
        $application = $this->get_application();
        
        if ($application instanceof FeedbackNotificationSupport)
        {
            return new NotificationService($application->get_notification_handlers());
        }
        
        return null;
    }

    /**
     * Notifies of a new feedback object
     * 
     * @param Feedback $feedback
     */
    public function notifyNewFeedback(Feedback $feedback)
    {
        $application = $this->get_application();
        
        if ($application instanceof FeedbackNotificationSupport)
        {
            $this->getNotificationService()->notify($feedback, $application->retrieve_notifications()->as_array());
        }
    }

    /**
     * @return bool
     */
    public function showFeedbackHeader()
    {
        $configuration = $this->getApplicationConfiguration()->get(self::CONFIGURATION_SHOW_FEEDBACK_HEADER);

        return isset($configuration) ? $configuration : true;
    }
}
