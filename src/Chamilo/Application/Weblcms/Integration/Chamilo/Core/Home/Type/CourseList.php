<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;

class CourseList extends Block
{

    public function displayContent()
    {
        $html = array();

        $renderer = new \Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer(
            $this,
            $this->getLinkTarget());
        $renderer->show_new_publication_icons();
        $html[] = $renderer->as_html();

        return implode(PHP_EOL, $html);
    }
}