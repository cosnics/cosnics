<?php
namespace Chamilo\Application\CasUser\Component;

use Chamilo\Application\CasUser\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class AccountComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Application\CasUser\Account\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
