<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class QuotaComponent extends Manager
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Quota\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
