<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class RequestComponent extends Manager /* implements DelegateComponent */
{

    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'RequestCourses');
        
        $factory = new ApplicationFactory(
            \Chamilo\Application\Weblcms\Request\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));

        return $factory->run();
    }
}