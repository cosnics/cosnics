<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ThemeComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {
        echo Theme::get_theme();
    }
}