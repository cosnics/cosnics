<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class LanguageComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Admin\Language\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
