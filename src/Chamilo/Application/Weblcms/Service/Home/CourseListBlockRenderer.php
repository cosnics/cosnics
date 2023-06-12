<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 */
class CourseListBlockRenderer extends BlockRenderer
{
    public const CONTEXT = Manager::CONTEXT;

    public function displayContent(Element $block, ?User $user = null): string
    {
        $renderer = new CourseListRenderer(
            $this, ''
        );

        $renderer->show_new_publication_icons();

        return $renderer->as_html();
    }

    public function renderContentFooter(Element $block): string
    {
        return '</div>';
    }

    public function renderContentHeader(Element $block): string
    {
        return '<div class="portal-block-content portal-block-course-list' . ($block->isVisible() ? '' : ' hidden') .
            '">';
    }
}