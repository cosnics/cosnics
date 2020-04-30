<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Browser;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Country;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\OperatingSystem;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Provider;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Referrer;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Login extends Event
{

    /**
     *
     * @see \Chamilo\Core\Tracking\Storage\DataClass\Event::getTrackerClasses()
     */
    public function getTrackerClasses()
    {
        return array(
            LoginLogout::class,
            Browser::class,
            Country::class,
            OperatingSystem::class,
            Provider::class,
            Referrer::class);
    }

    public function getType()
    {
        return 'login';
    }
}