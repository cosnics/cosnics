<?php
namespace Chamilo\Core\Admin\Announcement;

use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Admin\Announcement\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'announcement_action';
    const PARAM_SYSTEM_ANNOUNCEMENT_ID = 'announcement';
    const ACTION_CREATE = 'Creator';
    const ACTION_BROWSE = 'Browser';
    const ACTION_EDIT = 'Editor';
    const ACTION_DELETE = 'Deleter';
    const ACTION_VIEW = 'Viewer';
    const ACTION_HIDE = 'Hider';
    const DEFAULT_ACTION = self::ACTION_BROWSE;

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
