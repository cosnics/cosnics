<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

class ToolInvisibleComponent extends ToolVisibilityChangerComponent
{

    public function run()
    {
        $this->getRequest()->request->set(self::PARAM_VISIBILITY, 0);
        parent::run();
    }
}
