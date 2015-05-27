<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block;

abstract class ToolBlock extends CourseBlock
{

    private $params = array();

    public function get_user_id()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Manager :: PARAM_USERS);
    }

    public function get_tool()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL);
    }

    public function get_tool_registration($tool = null)
    {
        if (is_null($tool))
        {
            $tool = $this->get_tool();
        }
        
        return \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_course_tool_by_name($tool);
    }

    public function get_publication_id()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }

    public function get_category_id()
    {
        return $this->get_parent()->get_parameter(\Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY);
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
        $this->params[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $pid;
    }
}
