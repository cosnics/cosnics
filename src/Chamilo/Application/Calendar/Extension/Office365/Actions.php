<?php
namespace Chamilo\Application\Calendar\Extension\Office365;

use Chamilo\Application\Calendar\ActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Application\Calendar\Extension\Office365
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Actions implements ActionsInterface
{

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getAdditional(Application $application): array
    {
        return [];
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getPrimary(Application $application): array
    {
        return [];
    }
}