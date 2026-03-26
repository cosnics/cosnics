<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\UserInterface\HomeRenderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class BlockRenderer
{
    public const string BLOCK_PROPERTY_ID = 'id';
    public const string BLOCK_PROPERTY_IMAGE = 'image';
    public const string BLOCK_PROPERTY_NAME = 'name';
    public const string PARAM_ACTION = 'block_action';
    public const int SOURCE_AJAX = 2;
    public const int SOURCE_DEFAULT = 1;

    public function __construct(
        protected readonly HomeService $homeService, protected readonly UrlGenerator $urlGenerator,
        protected readonly Translator $translator
    )
    {
    }

    public function render(Element $block, ?User $user = null): string
    {
        $html = [];
        $html[] = $this->renderHeader($block);
        $html[] = $this->displayContent($block, $user);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    abstract public function displayContent(Element $block, ?User $user = null): string;

    public function displayTitle(Element $block): string
    {
        $html = [];

        $html[] = '<div class="card-header">';
        $html[] = '<h5 class="card-title">' . $this->getTitle($block) . '</h5>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getTitle(Element $block): string
    {
        return htmlspecialchars($block->getTitle());
    }

    /**
     * By default do not show on home page block when user is
     * not connected.
     */
    public function isVisible(Element $block, ?User $user = null): bool
    {
        return $block->isVisible() && $user instanceof User;
    }

    public function renderContentFooter(): string
    {
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderContentHeader(Element $block): string
    {
        $html = [];

        $html[] = '<div class="card-body">';

        return implode(PHP_EOL, $html);
    }

    public function renderFooter(): string
    {
        $html = [];

        $html[] = $this->renderContentFooter();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderHeader(Element $block): string
    {
        $html = [];

        $html[] =
            '<div class="card text-bg-light mb-3" data-column-id="' . $block->getParentId() . '" data-element-id="' .
            $block->getId() . '">';
        $html[] = $this->displayTitle($block);
        $html[] = $this->renderContentHeader($block);
        $html[] = '<div style="overflow:auto;">';

        return implode(PHP_EOL, $html);
    }
}
