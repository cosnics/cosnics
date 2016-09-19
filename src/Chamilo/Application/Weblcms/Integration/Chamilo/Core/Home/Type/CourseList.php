<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;

class CourseList extends Block
{

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
        return '<div class="portal-block-content portal-block-course-list' .
             ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
        return '</div>';
    }

    public function displayContent()
    {
        $renderer = new \Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer(
            $this,
            $this->getLinkTarget());
        $renderer->show_new_publication_icons();

        return $renderer->as_html();
    }
}