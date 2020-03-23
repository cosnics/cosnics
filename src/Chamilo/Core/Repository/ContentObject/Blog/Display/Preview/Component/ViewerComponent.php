<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Blog\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Blog\Display\Preview\Manager implements
    BlogDisplaySupport
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
