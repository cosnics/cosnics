<?php
namespace Chamilo\Core\Menu\Renderer\NavigationBarRenderer;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Renderer\NavigationBarRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class NavigationBarItemRenderer extends ItemRenderer
{
    /**
     * @var \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemService $itemService,
        Theme $themeUtilities, ChamiloRequest $request
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->itemService = $itemService;
        $this->themeUtilities = $themeUtilities;
        $this->request = $request;
    }

    /**
     * @return \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker(): AuthorizationCheckerInterface
    {
        return $this->authorizationChecker;
    }

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker): void
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities(): Theme
    {
        return $this->themeUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities(Theme $themeUtilities): void
    {
        $this->themeUtilities = $themeUtilities;
    }

    /**
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    /**
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest(ChamiloRequest $request): void
    {
        $this->request = $request;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    protected function renderCssIcon(Item $item)
    {
        $html = [];

        $html[] = '<div class="chamilo-menu-item-css-icon' .
            ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '">';
        $html[] = '<span class="chamilo-menu-item-css-icon-class ' . $item->getIconClass() . '"></span>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

}