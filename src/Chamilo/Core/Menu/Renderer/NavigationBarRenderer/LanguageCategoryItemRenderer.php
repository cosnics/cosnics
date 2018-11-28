<?php
namespace Chamilo\Core\Menu\Renderer\NavigationBarRenderer;

use Chamilo\Configuration\Service\LanguageConsulter;
use Chamilo\Core\Menu\Renderer\ItemRendererFactory;
use Chamilo\Core\Menu\Renderer\NavigationBarRenderer;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\LanguageItem;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Renderer\NavigationBarRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LanguageCategoryItemRenderer extends NavigationBarItemRenderer
{
    /**
     * @var \Chamilo\Configuration\Service\LanguageConsulter
     */
    private $languageConsulter;

    /**
     * @var \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Configuration\Service\LanguageConsulter $languageConsulter
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemService $itemService,
        Theme $themeUtilities, ChamiloRequest $request, LanguageConsulter $languageConsulter,
        ItemRendererFactory $itemRendererFactory
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemService, $themeUtilities, $request);

        $this->languageConsulter = $languageConsulter;
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    public function render(Item $item, User $user)
    {
        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $html = array();

        $html[] = '<li class="dropdown">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        $imagePath = $this->getThemeUtilities()->getImagePath('Chamilo\Core\Menu', 'Language');
        $title = $this->getItemService()->getItemTitleForCurrentLanguage($item);

        $html[] =
            '<img class="chamilo-menu-item-icon' . ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') .
            '" src="' . $imagePath . '" title="' . htmlentities($title) . '" alt="' . $title . '" />';

        $html[] = '<div class="chamilo-menu-item-label-with-image">';
        $html[] = strtoupper($this->getTranslator()->getLocale());
        $html[] = '<span class="caret"></span>';
        $html[] = '</div>';
        $html[] = '</a>';

        $html[] = $this->renderLanguageItems($item, $user);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @return \Chamilo\Configuration\Service\LanguageConsulter
     */
    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\LanguageConsulter $languageConsulter
     */
    public function setLanguageConsulter(LanguageConsulter $languageConsulter): void
    {
        $this->languageConsulter = $languageConsulter;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isItemVisibleForUser(User $user)
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\User', 'ChangeLanguage');
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    public function renderLanguageItems(Item $item, User $user)
    {
        $html = array();

        $languages = $this->getLanguageConsulter()->getLanguages();
        $currentLanguage = $this->getTranslator()->getLocale();

        if (count($languages) > 1)
        {
            $redirect = new Redirect();
            $currentUrl = $redirect->getCurrentUrl();

            $html[] = '<ul class="dropdown-menu language-selector">';

            foreach ($languages as $isocode => $language)
            {
                $redirect = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                        Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_QUICK_LANG,
                        \Chamilo\Core\User\Manager::PARAM_CHOICE => $isocode,
                        \Chamilo\Core\User\Manager::PARAM_REFER => $currentUrl
                    )
                );

                $languageItem = new LanguageItem();
                $languageItem->setLanguage($isocode);
                $languageItem->setCurrentUrl($currentUrl);
                $languageItem->setParentId($item->getId());

                if ($currentLanguage != $isocode)
                {
                    $itemRenderer =
                        $this->getItemRendererFactory()->getItemRenderer(NavigationBarRenderer::class, $languageItem);
                    $html[] = $itemRenderer->render($languageItem, $user);
                }
            }

            $html[] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }
}