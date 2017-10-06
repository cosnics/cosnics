<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package repository.lib.complex_builder.glossary.component
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        if (! $this->get_parent()->is_allowed_to_delete_child())
        {
            throw new NotAllowedException();
        }

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Display\Action\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
