<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Preview\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;

class ViewComponent extends \Chamilo\Core\Repository\ContentObject\Blog\Display\Preview\Manager implements
    BlogDisplaySupport
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\ContentObject\Blog\Display\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
