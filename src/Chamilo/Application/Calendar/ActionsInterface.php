<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Application\Calendar
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ActionsInterface
{

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getAdditional(Application $application): array;

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getPrimary(Application $application): array;
}