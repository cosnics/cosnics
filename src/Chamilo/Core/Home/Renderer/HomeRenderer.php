<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Service\AngularConnectorService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeRenderer
{
    protected AngularConnectorService $angularConnectorService;

    protected ConfigurationConsulter $configurationConsulter;

    protected HomeService $homeService;

    protected TabHeaderRenderer $tabHeaderRenderer;

    protected TabRenderer $tabRenderer;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        AngularConnectorService $angularConnectorService, ConfigurationConsulter $configurationConsulter,
        HomeService $homeService, Translator $translator, UrlGenerator $urlGenerator, WebPathBuilder $webPathBuilder,
        TabHeaderRenderer $tabHeaderRenderer, TabRenderer $tabRenderer
    )
    {
        $this->angularConnectorService = $angularConnectorService;
        $this->configurationConsulter = $configurationConsulter;
        $this->homeService = $homeService;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->webPathBuilder = $webPathBuilder;
        $this->tabHeaderRenderer = $tabHeaderRenderer;
        $this->tabRenderer = $tabRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     */
    public function render(?int $currentTabIdentifier = null, bool $isGeneralMode = false, ?User $user = null): string
    {
        $webPathBuilder = $this->getWebPathBuilder();

        $userHomeAllowed = $this->getHomeService()->isUserHomeAllowed();

        $isEditable = ($user instanceof User && ($userHomeAllowed || ($user->is_platform_admin() && $isGeneralMode)));
        $isGeneralMode = ($isGeneralMode && $user instanceof User && $user->is_platform_admin());

        if ($isEditable)
        {
            $html[] = '<script src="' . $webPathBuilder->getJavascriptPath('Chamilo\Core\Home') . 'HomeAjax.js' .
                '"></script>';
        }

        if ($isGeneralMode)
        {
            $html[] =
                '<script src="' . $webPathBuilder->getJavascriptPath('Chamilo\Core\Home') . 'HomeGeneralModeAjax.js' .
                '"></script>';
        }

        $html[] = $this->renderTabs($currentTabIdentifier, $isGeneralMode, $user);

        if ($isEditable)
        {
            $html[] = $this->renderTabTitlePanel();
        }

        if ($isGeneralMode)
        {
            $html[] = '<div class="alert alert-danger">' .
                $this->getTranslator()->trans('HomepageInGeneralMode', [], Manager::CONTEXT) . '</div>';
        }

        $html[] = $this->renderPackageContainer();
        $html[] = $this->renderContent($currentTabIdentifier, $isGeneralMode, $user);

        $html[] =
            '<script src="' . $webPathBuilder->getJavascriptPath('Chamilo\Core\Home') . 'HomeView.js' . '"></script>';

        return implode(PHP_EOL, $html);
    }

    public function getAngularConnectorService(): AngularConnectorService
    {
        return $this->angularConnectorService;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    protected function getHomeService(): HomeService
    {
        return $this->homeService;
    }

    public function getTabHeaderRenderer(): TabHeaderRenderer
    {
        return $this->tabHeaderRenderer;
    }

    public function getTabRenderer(): TabRenderer
    {
        return $this->tabRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrl(array $parameters = [], array $filter = []): string
    {
        return $this->getUrlGenerator()->fromParameters($parameters, $filter);
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
     * @throws \QuickformException
     */
    public function renderButtons(bool $isGeneralMode = false, ?User $user = null): string
    {
        $userHomeAllowed = $this->getHomeService()->isUserHomeAllowed();
        $homeUserIdentifier = $this->getHomeService()->determineHomeUserIdentifier($user);
        $translator = $this->getTranslator();

        $html = [];

        if ($user instanceof User && ($userHomeAllowed || $user->is_platform_admin()))
        {
            $buttonToolBar = new ButtonToolBar();

            if ($userHomeAllowed || $isGeneralMode)
            {
                $splitDropdownButton = new SplitDropdownButton(
                    $translator->trans('NewBlock', [], Manager::CONTEXT), new FontAwesomeGlyph('plus'), '#',
                    AbstractButton::DISPLAY_ICON_AND_LABEL, null, ['portal-add-block btn-link'], null,
                    ['dropdown-menu-right']
                );

                $buttonToolBar->addItem($splitDropdownButton);

                $splitDropdownButton->addSubButton(
                    new SubButton(
                        $translator->trans('NewColumn', [], Manager::CONTEXT), null, '#', AbstractButton::DISPLAY_LABEL,
                        null, ['portal-add-column', 'btn-link']
                    )
                );
                $splitDropdownButton->addSubButton(
                    new SubButton(
                        $translator->trans('NewTab', [], Manager::CONTEXT), null, '#', AbstractButton::DISPLAY_LABEL,
                        null, ['portal-add-tab', 'btn-link']
                    )
                );

                $truncateLink =
                    $this->getUrlGenerator()->fromParameters([Application::PARAM_ACTION => Manager::ACTION_TRUNCATE]);

                if ($homeUserIdentifier != '0')
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            $translator->trans('ResetHomepage', [], Manager::CONTEXT), null, $truncateLink,
                            AbstractButton::DISPLAY_LABEL,
                            $translator->trans('ConfirmChosenAction', [], StringUtilities::LIBRARIES),
                            ['portal-reset', 'btn-link']
                        )
                    );
                }
            }

            if (!$isGeneralMode && $user->is_platform_admin())
            {
                $homeUrl =
                    $this->getUrlGenerator()->fromParameters([Application::PARAM_ACTION => Manager::ACTION_MANAGE_HOME]
                    );

                $buttonToolBar->addItem(
                    new Button(
                        $translator->trans('ConfigureDefault', [], Manager::CONTEXT), new FontAwesomeGlyph('wrench'),
                        $homeUrl
                    )
                );
            }
            elseif ($isGeneralMode && $user->is_platform_admin())
            {
                $personalUrl =
                    $this->getUrlGenerator()->fromParameters([Application::PARAM_ACTION => Manager::ACTION_PERSONAL]);

                $title = $userHomeAllowed ? 'BackToPersonal' : 'ViewDefault';

                if (isset($splitDropdownButton))
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            $translator->trans($title, [], Manager::CONTEXT), new FontAwesomeGlyph('home'),
                            $personalUrl, AbstractButton::DISPLAY_LABEL
                        )
                    );
                }
                else
                {
                    $buttonToolBar->addItem(
                        new Button($translator->trans($title), new FontAwesomeGlyph('home'), $personalUrl)
                    );
                }
            }

            $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);
            $html[] = '<li class="pull-right portal-actions">' . $buttonToolBarRenderer->render() . '</li>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     */
    public function renderContent(?int $currentTabIdentifier = null, bool $isGeneralMode = false, ?User $user = null
    ): string
    {
        $angularConnectorService = $this->getAngularConnectorService();
        $tabRenderer = $this->getTabRenderer();

        $modules = $angularConnectorService->getAngularModules();
        $moduleString = count($modules) > 0 ? '\'' . implode('\', \'', $modules) . '\'' : '';

        $html = [];

        $html[] = $angularConnectorService->loadAngularModules();

        $html[] = '<script>';
        $html[] = '(function(){';
        $html[] = '    var homeApp = angular.module(\'homeApp\', [' . $moduleString . ']);';
        $html[] = '    homeApp.filter(\'arrayToString\', function() { return function(x) { return x; }; });';
        $html[] = '})();';
        $html[] = '</script>';

        $html[] = '<div class="portal-tabs" ng-app="homeApp">';

        $tabs = $this->getHomeService()->findElementsByTypeUserAndParentIdentifier(Tab::class, $user);

        foreach ($tabs as $tabKey => $tab)
        {
            $html[] = $tabRenderer->render($tab, $tabKey, $currentTabIdentifier, $isGeneralMode, $user);
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

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     */
    public function renderTabs(?int $currentTabIdentifier = null, bool $isGeneralMode = false, ?User $user = null
    ): string
    {
        $tabHeaderRenderer = $this->getTabHeaderRenderer();

        $html = [];

        $html[] = '<ul class="nav nav-tabs portal-nav-tabs">';

        $tabs = $this->getHomeService()->findElementsByTypeUserAndParentIdentifier(Tab::class, $user);

        foreach ($tabs as $tabKey => $tab)
        {
            $html[] = $tabHeaderRenderer->render(
                $tab, $tabKey, $currentTabIdentifier, $isGeneralMode, $user
            );
        }

        $html[] = $this->renderButtons($isGeneralMode, $user);

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
