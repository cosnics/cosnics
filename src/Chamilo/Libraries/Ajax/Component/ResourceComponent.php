<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResourceComponent extends \Chamilo\Libraries\Ajax\Manager implements NoAuthenticationSupport
{

    public function run()
    {
        \Chamilo\Libraries\Format\Utilities\ResourceUtilities :: launch();
    }
}