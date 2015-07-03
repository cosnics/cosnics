<?php
namespace Chamilo\Application\CasStorage\Component;

use Chamilo\Application\CasStorage\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class RightsComponent extends Manager
{

    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Application\CasStorage\Rights\Manager :: context(),
          new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
}
