<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Structure\Page;

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
        Page :: getInstance()->setSection('external_repository');
        \Chamilo\Core\Repository\External\Manager :: launch($this);
    }
}