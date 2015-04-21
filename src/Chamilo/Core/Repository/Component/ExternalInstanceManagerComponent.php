<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;

/**
 *
 * @package Chamilo\Core\Repository\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExternalInstanceManagerComponent extends Manager
{

    public function run()
    {
        \Chamilo\Core\Repository\External\Manager :: launch($this);
    }
}