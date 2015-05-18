<?php
namespace Chamilo\Application\CasUser\Component;

use Chamilo\Application\CasUser\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class RightsComponent extends Manager
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Application\CasUser\Rights\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
