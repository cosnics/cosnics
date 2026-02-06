<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\UserInterface\HomeRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeRenderer
{
    protected HomeService $homeService;

    protected TabRenderer $tabRenderer;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        HomeService $homeService, Translator $translator, UrlGenerator $urlGenerator, WebPathBuilder $webPathBuilder,
        TabRenderer $tabRenderer
    )
    {
        $this->homeService = $homeService;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->webPathBuilder = $webPathBuilder;
        $this->tabRenderer = $tabRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function render(?int $currentTabIdentifier = null, ?User $user = null): string
    {
        $html[] = $this->renderPackageContainer();
        $html[] = $this->renderContent($currentTabIdentifier, $user);

        return implode(PHP_EOL, $html);
    }

    protected function getHomeService(): HomeService
    {
        return $this->homeService;
    }

    public function getTabRenderer(): TabRenderer
    {
        return $this->tabRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function renderContent(?int $currentTabIdentifier = null, ?User $user = null): string
    {
        $tabRenderer = $this->getTabRenderer();

        $html = [];

        $html[] = '<div class="portal-tabs">';

        $tabs = $this->getHomeService()->findElementsByTypeAndParentIdentifier(Element::TYPE_TAB);

        foreach ($tabs as $tabKey => $tab) {
            $html[] = $tabRenderer->render($tab, $tabKey, $currentTabIdentifier, $user);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderPackageContainer(): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<form class="form-inline package-search">';
        $html[] = '<div class="form-group">';
        $html[] = '<div class="input-group">';

        $glyph = new FontAwesomeGlyph('search', [], null, 'fas');

        $html[] = '<div class="input-group-addon">' . $glyph->render() . '</div>';
        $html[] = '<input type="text" class="form-control" id="portal-package-name" placeholder="' .
            $translator->trans('SearchForWidgets', [], Manager::CONTEXT) . '">';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<select class="form-control" id="portal-package-context">';
        $html[] = '<option value="">' . $translator->trans('AllPackages', [], Manager::CONTEXT) . '</option>';
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</form>';

        $html[] = '<div class="row portal-package-blocks">';
        $html[] = '</div>';

        return $this->renderPanel(
            'portal-package-container', 'portal-action portal-package-hide',
            $translator->trans('BrowseBlocks', [], Manager::CONTEXT), implode(PHP_EOL, $html)
        );
    }

    public function renderPanel($rowClass, $actionClass, $title, $content): string
    {
        $html = [];

        $html[] = '<div class="row ' . $rowClass . ' hidden">';

        $html[] = '<div class="col-xs-12">';
        $html[] = '<div class="panel panel-primary">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<div class="pull-right">';

        $glyph = new FontAwesomeGlyph('times', [], null, 'fas');

        $html[] = '<a href="#" class="' . $actionClass . '">' . $glyph->render() . '</a>';
        $html[] = '</div>';
        $html[] = '<h3 class="panel-title">' . $title . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $content;
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderTabTitlePanel(): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<form class="form-inline portal-action-tab-form">';
        $html[] = '<div class="form-group">';
        $html[] = '<input type="text" class="form-control portal-action-tab-title" data-tab-id="" placeholder="' .
            $translator->trans('EnterTabTitle', [], Manager::CONTEXT) . '" />';
        $html[] = '</div>';

        $html[] = '<button type="submit" class="btn btn-primary portal-tab-title-save">' .
            $translator->trans('Save', [], Manager::CONTEXT) . '</button>';

        $html[] = '</form>';

        return $this->renderPanel(
            'portal-tab-panel', 'portal-tab-panel-hide', $translator->trans('EditTabTitle', [], Manager::CONTEXT),
            implode(PHP_EOL, $html)
        );
    }
}
