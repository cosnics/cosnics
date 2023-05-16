<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\AjaxComponent;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use RuntimeException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_DELETE_ENTRY_ATTACHMENT = 'delete_entry_attachment';
    public const ACTION_GET_NOTIFICATIONS = 'GetNotifications';
    public const ACTION_UPLOAD_ENTRY_ATTACHMENT = 'upload_entry_attachment';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_UPLOAD_ENTRY_ATTACHMENT;

    public const PARAM_ACTION = 'assignment_display_ajax_action';
    public const PARAM_ENTRY_ATTACHMENT_ID = 'entry_attachment_id';

    /**
     * @var AjaxComponent
     */
    protected $ajaxComponent;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (!$applicationConfiguration->getApplication() instanceof AjaxComponent)
        {
            throw new RuntimeException(
                'The ajax components from the assignment display manager can only be called from ' .
                'within the AjaxComponent of the assignment display application'
            );
        }

        $this->ajaxComponent = $applicationConfiguration->getApplication();

        parent::__construct($applicationConfiguration);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    protected function getDataProvider(): AssignmentDataProvider
    {
        return $this->ajaxComponent->getDataProvider();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface
     */
    protected function getNotificationServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(NotificationServiceBridgeInterface::class);
    }

}