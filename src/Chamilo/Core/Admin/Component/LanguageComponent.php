<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class LanguageComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->get_platformadmin())
        {
            throw new NotAllowedException();
        }

        $factory = new ApplicationFactory(
            \Chamilo\Core\Admin\Language\Manager :: context(),
           new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
}
