<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Blog\Display\Preview\Manager implements
    BlogDisplaySupport
{

    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Blog\Display\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
}
