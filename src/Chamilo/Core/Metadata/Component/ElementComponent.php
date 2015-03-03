<?php
namespace Chamilo\Core\Metadata\Component;

use Chamilo\Core\Metadata\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ElementComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Metadata\Element\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
