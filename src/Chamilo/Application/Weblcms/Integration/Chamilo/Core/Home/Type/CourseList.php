<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;

class CourseList extends Block
{

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
        return '<div class="portal-block-content portal-block-course-list' .
             ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';
    }

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
        return '</div>';
    }

    public function displayContent()
    {
        $renderer = new CourseListRenderer(
            $this, 
            $this->getLinkTarget());
        $renderer->show_new_publication_icons();
        
        return $renderer->as_html();
    }
}