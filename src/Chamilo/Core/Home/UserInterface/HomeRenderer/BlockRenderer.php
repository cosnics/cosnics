<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interface\AnonymousBlockInterface;
use Chamilo\Core\Home\Architecture\Interface\ReadOnlyBlockInterface;
use Chamilo\Core\Home\Architecture\Interface\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\UserInterface\HomeRenderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class BlockRenderer
{
    public const BLOCK_PROPERTY_ID = 'id';
    public const BLOCK_PROPERTY_IMAGE = 'image';
    public const BLOCK_PROPERTY_NAME = 'name';

    public const PARAM_ACTION = 'block_action';

    public const SOURCE_AJAX = 2;
    public const SOURCE_DEFAULT = 1;

    protected ConfigurationConsulter $configurationConsulter;

    protected HomeService $homeService;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter
    )
    {
        $this->homeService = $homeService;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->configurationConsulter = $configurationConsulter;
    }

    public function render(Element $block, ?User $user = null): string
    {
        if (!$this instanceof AnonymousBlockInterface && !$this->isVisible($block, $user))
        {
            return '';
        }

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

        $html[] = '<div class="panel-heading' . ($block->isVisible() ? '' : ' panel-heading-without-content') . '">';
        $html[] = '<h3 class="panel-title">' . $this->getTitle($block) . '</h3>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getHomeService(): HomeService
    {
        return $this->homeService;
    }

    public function getTitle(Element $block): string
    {
        return htmlspecialchars($block->getTitle());
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function hasStaticTitle(): bool
    {
        return $this instanceof StaticBlockTitleInterface;
    }

    public function isReadOnly(): bool
    {
        return $this instanceof ReadOnlyBlockInterface;
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
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderContentHeader(Element $block): string
    {
        $html = [];

        $html[] = '<div class="portal-block-content' . ($block->isVisible() ? '' : ' hidden') . '">';
        $html[] = '<div class="panel-body">';

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

        $html[] = '<div class="panel panel-default portal-block" data-column-id="' . $block->getParentId() .
            '" data-element-id="' . $block->getId() . '">';
        $html[] = $this->displayTitle($block);
        $html[] = $this->renderContentHeader($block);
        $html[] = '<div style="overflow:auto;">';

        return implode(PHP_EOL, $html);
    }

}
