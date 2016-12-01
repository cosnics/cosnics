<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * 
 * @package application.lib.weblcms.weblcms_manager.component
 */
class SubscribeAsAdminComponent extends SubscribeComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        Request::set_get(self::PARAM_STATUS, 1);
        parent::run();
    }
}
