<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class BrowserComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Lynx\Manager\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
