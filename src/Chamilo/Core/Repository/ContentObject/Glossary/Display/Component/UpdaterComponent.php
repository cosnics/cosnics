<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 *
 * @package repository.lib.complex_builder.glossary.component
 */
class UpdaterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! $this->get_parent()->is_allowed_to_add_child())
        {
            throw new NotAllowedException();
        }

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Display\Action\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }
}
