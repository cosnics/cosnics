<?php
namespace Chamilo\Application\CasStorage\Component;

use Chamilo\Application\CasStorage\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ServiceComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Application\CasStorage\Service\Manager :: context(),
           new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
}
