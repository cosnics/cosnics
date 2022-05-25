<?php
namespace Chamilo\Core\Repository\Feedback;

use Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsServiceBridgeAdapter;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsServiceBridgeInterface;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeAdapter;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationService;
use Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationServiceInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Exception;

abstract class Manager extends Application
{
    const ACTION_BROWSE = 'Browser';

    const ACTION_BROWSE_V2 = 'BrowserV2';

    // Parameters

    const ACTION_DELETE = 'Deleter';

    const ACTION_SUBSCRIBER = 'Subscriber';

    // Actions

    const ACTION_UNSUBSCRIBER = 'Unsubscriber';

    const ACTION_UPDATE = 'Updater';

    const CONFIGURATION_SHOW_FEEDBACK_HEADER = 'showFeedbackHeader';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_ACTION = 'feedback_action';

    const PARAM_FEEDBACK_BRIDGE = 'FeedbackBridge';

    // Default action

    const PARAM_FEEDBACK_ID = 'feedback_id';

    const PARAM_FEEDBACK_RIGHTS_BRIDGE = 'FeedbackRightsBridge';

    /**
     * @var \Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface
     */
    protected $feedbackServiceBridge;

    /**
     * @var \Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsServiceBridgeInterface
     */
    protected $feedbackRightsServiceBridge;

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

        if (!$this->get_application() instanceof FeedbackSupport)
        {
            throw new NotAllowedException();
        }

        $this->initializeBridges($applicationConfiguration);
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
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    protected function initializeBridges(ApplicationConfigurationInterface $applicationConfiguration)
    {
        try
        {
            $this->feedbackServiceBridge =
                $this->getBridgeManager()->getBridgeByInterface(FeedbackServiceBridgeInterface::class);
        }
        catch (Exception $ex)
        {
            /** @var \Chamilo\Core\Repository\Feedback\FeedbackSupport $application */
            $application = $this->get_application();
            $this->feedbackServiceBridge = new FeedbackServiceBridgeAdapter($application);
        }

        try
        {
            $this->feedbackRightsServiceBridge =
                $this->getBridgeManager()->getBridgeByInterface(FeedbackRightsServiceBridgeInterface::class);
        }
        catch (Exception $ex)
        {
            /** @var \Chamilo\Core\Repository\Feedback\FeedbackSupport $application */
            $application = $this->get_application();
            $this->feedbackRightsServiceBridge = new FeedbackRightsServiceBridgeAdapter($application);
        }
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
            $notifications = $application->retrieve_notifications();
            $notifications = $notifications->toArray();

            $this->getNotificationService()->notify($feedback, $notifications);
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
