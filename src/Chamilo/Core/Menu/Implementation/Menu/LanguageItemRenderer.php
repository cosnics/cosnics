<?php
namespace Chamilo\Core\Menu\Implementation\Menu;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Implementation\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LanguageItemRenderer extends ItemRenderer
{
    protected UrlGenerator $urlGenerator;

    private ItemRendererRegistry $itemRendererFactory;

    private LanguageConsulter $languageConsulter;

    public function __construct(
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        LanguageConsulter $languageConsulter, ItemRendererRegistry $itemRendererFactory, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($translator, $itemCacheService, $request);

        $this->languageConsulter = $languageConsulter;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function render(Item $item, User $user): string
    {
        $languages = $this->getLanguageConsulter()->getOtherLanguages($this->getTranslator()->getLocale());

        if (count($languages) > 1) {
            return $this->renderDropdown($item);
        }
        else {
            $html = [];

            foreach ($languages as $isocode => $language) {
                $languageUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => ActionEnum::LANGUAGE->value,
                        Manager::PARAM_LANGUAGE => $isocode,
                        Manager::PARAM_REFER => $this->getRequest()->getUri()
                    ]
                );

                $html = [];

                $html[] = '<li class="nav-item">';
                $html[] = '<a class="text-center nav-link" href="' . $languageUrl . '">';

                if ($item->showIcon()) {
                    $html[] = $this->getRenderedGlyph();
                }

                if ($item->showTitle()) {
                    $html[] = '<div>' . $language . '</div>';
                }

                $html[] = '</a>';
                $html[] = '</li>';
            }

            return implode(PHP_EOL, $html);
        }
    }

    public function getItemRendererFactory(): ItemRendererRegistry
    {
        return $this->itemRendererFactory;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    public function getRenderedGlyph(): string
    {
        $glyph = $this->getRendererTypeGlyph();
        $glyph->setExtraClasses(['fa-lg', 'fa-fw']);

        return $glyph->render();
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('language', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('LanguageItem', [], \Chamilo\Core\Menu\Manager::CONTEXT);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function renderDropdown(Item $item): string
    {
        $html = [];

        $html[] = '<li class="nav-item dropdown">';
        $html[] =
            '<a href="#" class="text-center nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">';

        $title = strtoupper($this->getTranslator()->getLocale());

        if ($item->showIcon()) {
            $html[] = $this->getRenderedGlyph();
        }

        if ($item->showTitle()) {
            $html[] = '<span>' . $title . '</span>';
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

        if (count($languages) > 1) {
            $currentUrl = $this->getRequest()->getUri();

            $html[] = '<ul class="dropdown-menu">';

            foreach ($languages as $isocode => $language) {
                $languageUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => ActionEnum::LANGUAGE->value,
                        Manager::PARAM_LANGUAGE => $isocode,
                        Manager::PARAM_REFER => $currentUrl
                    ]
                );

                if ($currentLanguage != $isocode) {
                    $html[] = '<li>';
                    $html[] = '<a class="dropdown-item" href="' . $languageUrl . '">';
                    $html[] = $language;
                    $html[] = '</a>';
                    $html[] = '</li>';
                }
            }

            $html[] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->getTranslator()->trans('LanguageItem', [], \Chamilo\Core\Menu\Manager::CONTEXT);
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->getTranslator()->trans('LanguageItem', [], \Chamilo\Core\Menu\Manager::CONTEXT, $isoCode);
    }
}