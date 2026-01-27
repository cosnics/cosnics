<?php
namespace Chamilo\Application\Calendar\Architecture\Interface;

use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Application\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarExtensionActionProviderInterface
{

    /**
     * @return \Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\AbstractButtonToolBarItem[]
     */
    public function getAdditional(Application $application): array;

    /**
     * @return \Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\AbstractButtonToolBarItem[]
     */
    public function getPrimary(Application $application): array;
}