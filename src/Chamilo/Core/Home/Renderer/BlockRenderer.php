<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockRendererInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ReadOnlyBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Form\ConfigurationFormFactory;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Rights\Form\ElementTargetEntitiesForm;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\Renderer
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

    protected ConfigurationFormFactory $configurationFormFactory;

    protected ElementRightsService $elementRightsService;

    protected HomeService $homeService;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, ElementRightsService $elementRightsService,
        ConfigurationFormFactory $configurationFormFactory
    )
    {
        $this->homeService = $homeService;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->configurationConsulter = $configurationConsulter;
        $this->elementRightsService = $elementRightsService;
        $this->configurationFormFactory = $configurationFormFactory;
    }

    /**
     * @throws \QuickformException
     */
    public function render(Element $block, bool $isGeneralMode = false, ?User $user = null): string
    {
        if (!$this instanceof AnonymousBlockInterface && !$this->isVisible($block, $user))
        {
            return '';
        }

        $html = [];
        $html[] = $this->renderHeader($block, $isGeneralMode, $user);
        $html[] = $this->displayContent($block, $user);
        $html[] = $this->renderFooter($block);

        return implode(PHP_EOL, $html);
    }

    public function displayActions(Element $block, bool $isGeneralMode = false, ?User $user = null): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $userHomeAllowed = $this->getHomeService()->isUserHomeAllowed();
        $isIdentifiedUser = $user && !$user->is_anonymous_user();

        if ($user instanceof User && ($userHomeAllowed || $isGeneralMode) && $isIdentifiedUser)
        {
            if (!$this->isReadOnly())
            {
                $glyphVisible = new FontAwesomeGlyph('chevron-down');
                $textVisible = $translator->trans('ShowBlock', [], Manager::CONTEXT);

                $html[] = '<a href="#" class="portal-action portal-action-block-show' .
                    (!$block->isVisible() ? '' : ' hidden') . '" title="' . $textVisible . '">' .
                    $glyphVisible->render() . '</a>';

                $glyphVisible = new FontAwesomeGlyph('chevron-up');
                $textVisible = $translator->trans('HideBlock', [], Manager::CONTEXT);

                $html[] = '<a href="#" class="portal-action portal-action-block-hide' .
                    (!$block->isVisible() ? ' hidden' : '') . '" title="' . $textVisible . '">' .
                    $glyphVisible->render() . '</a>';
            }

            if ($isGeneralMode)
            {
                $glyph = new FontAwesomeGlyph('user');
                $configure_text = $translator->trans('SelectTargetUsersGroups', [], Manager::CONTEXT);

                $html[] = '<a href="#" class="portal-action portal-action-block-configure-target-entities" title="' .
                    $configure_text . '">' . $glyph->render() . '</a>';
            }

            if ($this instanceof ConfigurableBlockRendererInterface)
            {
                $glyph = new FontAwesomeGlyph('wrench');
                $configure_text = $translator->trans('Configure', [], StringUtilities::LIBRARIES);

                $html[] =
                    '<a href="#" class="portal-action portal-action-block-configure" title="' . $configure_text . '">' .
                    $glyph->render() . '</a>';
            }

            if (!$this->isReadOnly())
            {
                $glyph = new FontAwesomeGlyph('times');
                $delete_text = $translator->trans('Delete', [], StringUtilities::LIBRARIES);

                $html[] = '<a href="#" class="portal-action portal-action-block-delete" title="' . $delete_text . '">' .
                    $glyph->render() . '</a>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    abstract public function displayContent(Element $block, ?User $user = null): string;

    public function displayTitle(Element $block, bool $isGeneralMode = false, ?User $user = null): string
    {
        $html = [];

        $html[] = '<div class="panel-heading' . ($block->isVisible() ? '' : ' panel-heading-without-content') . '">';
        $html[] = '<div class="pull-right">' . $this->displayActions($block, $isGeneralMode, $user) . '</div>';
        $html[] = '<h3 class="panel-title">' . $this->getTitle($block, $user) . '</h3>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getConfigurationFormFactory(): ConfigurationFormFactory
    {
        return $this->configurationFormFactory;
    }

    public function getElementRightsService(): ElementRightsService
    {
        return $this->elementRightsService;
    }

    public function getHomeService(): HomeService
    {
        return $this->homeService;
    }

    public function getTitle(Element $block, ?User $user = null): string
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
        return $user instanceof User;
    }

    public function renderContentFooter(Element $block): string
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

    public function renderFooter(Element $block): string
    {
        $html = [];

        $html[] = $this->renderContentFooter($block);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function renderHeader(Element $block, bool $isGeneralMode = false, ?User $user = null): string
    {
        $html = [];
        $html[] = '<div class="panel panel-default portal-block" data-column-id="' . $block->getParentId() .
            '" data-element-id="' . $block->getId() . '">';
        $html[] = $this->displayTitle($block, $isGeneralMode, $user);

        if ($this instanceof ConfigurableBlockRendererInterface)
        {
            $html[] = '<div class="portal-block-form hidden">';
            $html[] = '<div class="panel-body">';

            $form =
                $this->getConfigurationFormFactory()->buildConfigurationForm($this, $block, $this->hasStaticTitle());
            $html[] = $form->render();

            $html[] = '</div>';
            $html[] = '</div>';
        }

        if ($isGeneralMode)
        {
            $html[] = '<div class="portal-block-target-entities-form hidden">';
            $html[] = '<div class="panel-body">';

            $form = new ElementTargetEntitiesForm(
                $block,
                $this->getUrlGenerator()->fromParameters([Application::PARAM_CONTEXT => Manager::ACTION_VIEW_HOME]),
                $this->getElementRightsService()
            );

            $html[] = $form->render();

            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[] = $this->renderContentHeader($block);

        $html[] = '<div style="overflow:auto;">';

        return implode(PHP_EOL, $html);
    }

}
