<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

class ToolVisibleComponent extends ToolVisibilityChangerComponent
{

    public function run()
    {
        $this->getRequest()->request->set(self::PARAM_VISIBILITY, 1);
        parent::run();
    }
}
