<?php
namespace Chamilo\Core\Admin\Announcement;

use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Admin\Announcement\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_EDIT = 'Editor';
    public const ACTION_HIDE = 'Hider';
    public const ACTION_VIEW = 'Viewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'announcement_action';
    public const PARAM_SYSTEM_ANNOUNCEMENT_ID = 'announcement';

    /**
     * @return \Chamilo\Core\Admin\Announcement\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->getService(PublicationService::class);
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->getService(RightsService::class);
    }
}
