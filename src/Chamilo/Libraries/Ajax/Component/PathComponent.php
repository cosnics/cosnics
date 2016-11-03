<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PathComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {
        echo Path :: get($_POST['Path']);
    }
}