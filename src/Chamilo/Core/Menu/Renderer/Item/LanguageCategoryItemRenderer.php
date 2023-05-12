<?php
namespace Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\LanguageItem;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LanguageCategoryItemRenderer extends ItemRenderer
{
    /**
     * @var \Chamilo\Configuration\Service\Consulter\LanguageConsulter
     */
    private $languageConsulter;

    /**
     * @var \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\CachedItemService $itemCacheService
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Configuration\Service\Consulter\LanguageConsulter $languageConsulter
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, CachedItemService $itemCacheService,
        ThemePathBuilder $themePathBuilder, ChamiloRequest $request, LanguageConsulter $languageConsulter,
        ItemRendererFactory $itemRendererFactory
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $themePathBuilder, $request);

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

        $languages = $this->getLanguageConsulter()->getOtherLanguages($this->getTranslator()->getLocale());

        if (count($languages) > 1)
        {
            return $this->renderDropdown($item, $user);
        }
        else
        {
            foreach ($languages as $isocode => $language)
            {
                $redirect = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_QUICK_LANG,
                        Manager::PARAM_CHOICE => $isocode,
                        Manager::PARAM_REFER => $this->getRequest()->getUri()
                    )
                );

                $html = [];

                $html[] = '<li>';
                $html[] = '<a href="' . $redirect->getUrl() . '">';

                if ($item->showIcon())
                {
                    $html[] = $this->getRenderedGlyph(!$item->showTitle());
                }

                if ($item->showTitle())
                {
                    $html[] = '<div>' . $language . '</div>';
                }

                $html[] = '</a>';
                $html[] = '</li>';

                return implode(PHP_EOL, $html);
            }
        }
    }

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @return \Chamilo\Configuration\Service\Consulter\LanguageConsulter
     */
    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\Consulter\LanguageConsulter $languageConsulter
     */
    public function setLanguageConsulter(LanguageConsulter $languageConsulter): void
    {
        $this->languageConsulter = $languageConsulter;
    }

    /**
     * @param bool $showCaret
     *
     * @return string
     */
    public function getRenderedGlyph(bool $showCaret = false)
    {
        $glyph = new FontAwesomeGlyph('language', array('fa-2x', 'fa-fw'), null, 'fas');

        $html = [];

        $html[] = '<div>';

        $html[] = $glyph->render();

        if ($showCaret)
        {
            $html[] = '&nbsp;<span class="caret"></span>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
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
    public function renderDropdown(Item $item, User $user): string
    {
        $html = [];

        $html[] = '<li class="dropdown">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        $title = $this->renderTitle($item);

        if ($item->showIcon())
        {
            $html[] = $this->getRenderedGlyph(!$item->showTitle());
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '&nbsp;<span class="caret"></span></div>';
        }

        $html[] = '</a>';

        $html[] = $this->renderDropdownItems($item, $user);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    public function renderDropdownItems(Item $item, User $user)
    {
        $html = [];

        $languages = $this->getLanguageConsulter()->getLanguages();
        $currentLanguage = $this->getTranslator()->getLocale();

        if (count($languages) > 1)
        {
            $redirect = new Redirect();
            $currentUrl = $redirect->getCurrentUrl();

            $html[] = '<ul class="dropdown-menu">';

            foreach ($languages as $isocode => $language)
            {
                $languageItem = new LanguageItem();
                $languageItem->setIsocode($isocode);
                $languageItem->setLanguage($language);
                $languageItem->setCurrentUrl($currentUrl);
                $languageItem->setParentId($item->getId());

                if ($currentLanguage != $isocode)
                {
                    $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($languageItem);
                    $html[] = $itemRenderer->render($languageItem, $user);
                }
            }

            $html[] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return strtoupper($this->getTranslator()->getLocale());
    }
}