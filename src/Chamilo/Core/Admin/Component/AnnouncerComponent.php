<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class AnnouncerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Admin\Announcement\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
