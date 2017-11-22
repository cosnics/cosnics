<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Component;

use Chamilo\Core\Repository\ContentObject\Blog\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 *
 * @package repository.lib.complex_builder.blog.component
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Display\Action\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
