<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

/**
 * @package application.lib.weblcms.weblcms_manager.component
 */
class SubscribeAsAdminComponent extends SubscribeComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->getRequest()->request->set(self::PARAM_STATUS, 1);
        parent::run();
    }
}
