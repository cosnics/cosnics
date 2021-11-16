<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AjaxComponent extends Manager
{
    /**
     *
     * @return string
     */
    function run()
    {
        $this->getApplicationFactory()->getApplication(
            'Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax',
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }
}
