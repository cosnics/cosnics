<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class DeleterComponent extends Manager
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Display\Action\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
