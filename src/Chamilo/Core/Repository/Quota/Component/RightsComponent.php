<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class RightsComponent extends Manager
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Quota\Rights\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
