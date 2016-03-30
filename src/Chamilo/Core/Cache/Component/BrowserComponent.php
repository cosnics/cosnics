<?php
namespace Chamilo\Core\Cache\Component;

use Chamilo\Core\Cache\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package Chamilo\Core\Cache\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->get_platformadmin())
        {
            throw new NotAllowedException();
        }
    }
}
