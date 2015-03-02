<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class AnnouncerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Admin\Announcement\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
