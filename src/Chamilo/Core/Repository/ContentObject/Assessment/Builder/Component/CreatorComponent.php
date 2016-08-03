<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.glossary.component
 */
class CreatorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\Builder\Action\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_TYPE);
    }
}
