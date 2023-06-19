<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LanguageItemRenderer extends ItemRenderer
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
            return $this->renderDropdown($item);
        }
        else
        {
            $html = [];

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
            }

            return implode(PHP_EOL, $html);
        }
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    public function getRenderedGlyph(bool $showCaret = false): string
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

    public function isItemVisibleForUser(User $user): bool
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\User', 'ChangeLanguage');
    }

    public function renderDropdown(Item $item): string
    {
        $html = [];

        $html[] = '<li class="dropdown">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        $title = strtoupper($this->getTranslator()->getLocale());

        if ($item->showIcon())
        {
            $html[] = $this->getRenderedGlyph(!$item->showTitle());
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '&nbsp;<span class="caret"></span></div>';
        }

        $html[] = '</a>';

        $html[] = $this->renderDropdownItems();

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function renderDropdownItems(): string
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
                $languageUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_QUICK_LANG,
                        Manager::PARAM_CHOICE => $isocode,
                        Manager::PARAM_REFER => $currentUrl
                    ]
                );

                if ($currentLanguage != $isocode)
                {
                    $html[] = '<li>';
                    $html[] = '<a href="' . $languageUrl . '">';
                    $html[] = '<div>';
                    $html[] = strtoupper($language);
                    $html[] = '</div>';
                    $html[] = '</a>';
                    $html[] = '</li>';
                }
            }

            $html[] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }
}