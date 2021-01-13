<?php

namespace Chamilo\Core\Notification\Domain;

use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Core\Notification\Domain
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationRedirect extends Redirect
{
    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->addParametersToUrl('index.php');
    }
}
