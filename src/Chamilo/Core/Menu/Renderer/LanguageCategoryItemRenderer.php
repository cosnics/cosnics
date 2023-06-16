<?php
namespace Chamilo\Core\Menu\Renderer;

use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\LanguageItem;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Renderer\ItemRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LanguageCategoryItemRenderer extends ItemRenderer
{
    protected UrlGenerator $urlGenerator;

    private ItemRendererFactory $itemRendererFactory;

    private LanguageConsulter $languageConsulter;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, LanguageConsulter $languageConsulter,
        ItemRendererFactory $itemRendererFactory, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->languageConsulter = $languageConsulter;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    public function render(Item $item, User $user): string
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
                $languageUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_QUICK_LANG,
                        Manager::PARAM_CHOICE => $isocode,
                        Manager::PARAM_REFER => $this->getRequest()->getUri()
                    ]
                );

                $html = [];

                $html[] = '<li>';
                $html[] = '<a href="' . $languageUrl . '">';

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
     * @return \Chamilo\Configuration\Service\Consulter\LanguageConsulter
     */
    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    /**
     * @param bool $showCaret
     *
     * @return string
     */
    public function getRenderedGlyph(bool $showCaret = false)
    {
        $glyph = new FontAwesomeGlyph('language', ['fa-2x', 'fa-fw'], null, 'fas');

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

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
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

    public function isSelected(Item $item): bool
    {
        return false;
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
            $currentUrl = $this->getRequest()->getUri();

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
    public function renderTitle(Item $item): string
    {
        return strtoupper($this->getTranslator()->getLocale());
    }

    /**
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Configuration\Service\Consulter\LanguageConsulter $languageConsulter
     */
    public function setLanguageConsulter(LanguageConsulter $languageConsulter): void
    {
        $this->languageConsulter = $languageConsulter;
    }
}