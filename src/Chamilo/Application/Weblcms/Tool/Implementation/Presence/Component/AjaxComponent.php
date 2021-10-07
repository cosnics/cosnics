<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Presence\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Presence\Component
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
            'Chamilo\Application\Weblcms\Tool\Implementation\Presence\Ajax',
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        )->run();
    }
}
