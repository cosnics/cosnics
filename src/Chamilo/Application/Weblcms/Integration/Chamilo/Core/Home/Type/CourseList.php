<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;

class CourseList extends Block
{

    public function display_content()
    {
        $html = array();
        
        $renderer = new \Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer(
            $this, 
            $this->get_link_target());
        $renderer->show_new_publication_icons();
        $html[] = $renderer->as_html();
        
        return implode("\n", $html);
    }

    /**
     * We need to override this because else we would redirect to the home page
     * 
     * @param $parameters
     */
    public function get_link($parameters)
    {
        return $this->get_parent()->get_link($parameters);
    }
}