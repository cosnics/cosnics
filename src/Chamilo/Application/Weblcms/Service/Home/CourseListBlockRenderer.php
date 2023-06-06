<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;

class CourseListBlockRenderer extends BlockRenderer
{

    public function displayContent()
    {
        $renderer = new CourseListRenderer(
            $this, $this->getLinkTarget()
        );
        $renderer->show_new_publication_icons();

        return $renderer->as_html();
    }

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
        return '</div>';
    }

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
        return '<div class="portal-block-content portal-block-course-list' .
            ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';
    }
}