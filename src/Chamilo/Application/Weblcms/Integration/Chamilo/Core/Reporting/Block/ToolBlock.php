<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Manager;

abstract class ToolBlock extends CourseBlock
{

    private $params = [];

    public function get_user_id()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);
    }

    public function get_tool()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL);
    }

    public function get_tool_registration($tool = null)
    {
        if (is_null($tool))
        {
            $tool = $this->get_tool();
        }
        
        return DataManager::retrieve_course_tool_by_name($tool);
    }

    public function getPublicationId()
    {
        return $this->get_parent()->get_parameter(Manager::PARAM_PUBLICATION_ID);
    }

    public function get_category_id()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY);
    }

    public function get_params()
    {
        return $this->params;
    }

    public function set_params($course_id, $user_id, $tool, $pid)
    {
        $this->params['course_id'] = $course_id;
        $this->params['user_id'] = $user_id;
        $this->params['tool'] = $tool;
        $this->params[Manager::PARAM_PUBLICATION_ID] = $pid;
    }
}
