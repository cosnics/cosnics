<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentActivity extends Block
{
    use DependencyInjectionContainerTrait;

    public function displayContent()
    {
        $this->initializeContainer();
        $user = $this->getUser();
    }

    /**
     * @return \Chamilo\Core\Notification\Service\NotificationManager
     */
    protected function getNotificationManager()
    {
        return $this->getService(NotificationManager::class);
    }
}