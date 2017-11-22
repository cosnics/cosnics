<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class RequestComponent extends Manager
{

    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'RequestCourses');

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Application\Weblcms\Request\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}