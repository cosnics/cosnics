<?php
namespace Chamilo\Application\CasUser\Component;

use Chamilo\Application\CasUser\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class AccountComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Application\CasUser\Account\Manager :: context(),
           new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
}
