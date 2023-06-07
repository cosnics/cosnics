<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 */
class CourseListBlockRenderer extends BlockRenderer
{

    public function displayContent(Block $block, ?User $user = null): string
    {
        $renderer = new CourseListRenderer(
            $this, ''
        );

        $renderer->show_new_publication_icons();

        return $renderer->as_html();
    }

    public function renderContentFooter(Block $block): string
    {
        return '</div>';
    }

    public function renderContentHeader(Block $block): string
    {
        return '<div class="portal-block-content portal-block-course-list' . ($block->isVisible() ? '' : ' hidden') .
            '">';
    }
}