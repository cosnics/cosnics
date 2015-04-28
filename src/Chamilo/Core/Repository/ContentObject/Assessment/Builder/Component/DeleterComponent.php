<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.glossary.component
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\Builder\Action\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }
}
