<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Login extends Event
{
    public const CONTEXT = 'Chamilo\Core\User\Integration\Chamilo\Core\Tracking';

    /**
     * @see \Chamilo\Core\Tracking\Storage\DataClass\Event::getTrackerClasses()
     */
    public function getTrackerClasses()
    {
        return [
            LoginLogout::class
        ];
    }

    public function getType()
    {
        return 'login';
    }
}