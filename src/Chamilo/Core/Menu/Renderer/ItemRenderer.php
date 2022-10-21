<?php
namespace Chamilo\Core\Menu\Renderer;

use Chamilo\Core\Menu\Service\ItemCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Renderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ItemRenderer
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
     * @var \Chamilo\Core\Menu\Service\ItemCacheService
     */
    private $itemCacheService;

    /**
     * @var \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    private $themePathBuilder;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemCacheService $itemCacheService
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemCacheService $itemCacheService,
        ThemePathBuilder $themePathBuilder, ChamiloRequest $request
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->itemCacheService = $itemCacheService;
        $this->themePathBuilder = $themePathBuilder;
        $this->request = $request;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    abstract public function render(Item $item, User $user);

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
     * @param bool $isSelected
     * @param string[] $existingClasses
     *
     * @return string[]
     */
    protected function getClasses($isSelected = false, $existingClasses = [])
    {
        if ($isSelected)
        {
            $existingClasses[] = 'active';
        }

        return $existingClasses;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemCacheService
     */
    public function getItemCacheService(): ItemCacheService
    {
        return $this->itemCacheService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemCacheService $itemCacheService
     */
    public function setItemCacheService(ItemCacheService $itemCacheService): void
    {
        $this->itemCacheService = $itemCacheService;
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
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder(): ThemePathBuilder
    {
        return $this->themePathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     */
    public function setThemePathBuilder(ThemePathBuilder $themePathBuilder): void
    {
        $this->themePathBuilder = $themePathBuilder;
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     */
    public function isSelected(Item $item)
    {
        return false;
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

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function renderTitle(Item $item)
    {
        return $this->getItemCacheService()->getItemTitleForCurrentLanguage($item);
    }
}