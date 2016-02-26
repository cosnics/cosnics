<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Actions
{

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicVisualTab[]
     */
    public function get(Application $application)
    {
        return array();
    }
}