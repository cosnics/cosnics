<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Application\Weblcms\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ChangeCourseModuleVisibilityComponent extends Manager
{

    public function run()
    {
        $module_id = $this->getRequest()->request->get('tool');
        $visible = $this->getRequest()->request->get('visible');
        $course = $this->getRequest()->request->get('course');

        DataManager::set_tool_visibility_by_tool_id(
            $course, $module_id, $visible
        );

        JsonAjaxResult::success();
    }
}