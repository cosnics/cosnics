<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ApplicationRightsComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Application\Survey\Rights\Application\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
