<?php
namespace Chamilo\Core\Home\Renderer\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Renderer\Type\Basic\TabHeaderRenderer;
use Chamilo\Core\Home\Renderer\Type\Basic\TabRenderer;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Service\AngularConnectorService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Basic extends Renderer
{

    /**
     * Caching variable to check if the home page is in general mode
     *
     * @var bool
     */
    protected $generalMode;

    /**
     * @var \Chamilo\Core\Home\Storage\DataClass\Element[]
     */
    private $elements;

    /**
     * @var \Chamilo\Core\Home\Service\HomeService
     */
    private $homeService;

    /**
     * @var int
     */
    private $homeUserIdentifier;

    /**
     * @see \Chamilo\Core\Home\Renderer\Renderer::render()
     */
    public function render()
    {
        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(WebPathBuilder::class);

        $user = $this->get_user();

        $userHomeAllowed = Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_user_home']);
        $generalMode = $this->isGeneralMode();

        $isEditable = ($user instanceof User && ($userHomeAllowed || ($user->is_platform_admin() && $generalMode)));
        $isGeneralMode = ($generalMode && $user instanceof User && $user->is_platform_admin());

        if ($isEditable)
        {
            $html[] = '<script src="' . $webPathBuilder->getJavascriptPath('Chamilo\Core\Home') . 'HomeAjax.js' .
                '"></script>';
        }

        if ($this->isGeneralMode())
        {
            $html[] =
                '<script src="' . $webPathBuilder->getJavascriptPath('Chamilo\Core\Home') . 'HomeGeneralModeAjax.js' .
                '"></script>';
        }

        $html[] = $this->renderTabs();

        if ($isEditable)
        {
            $html[] = $this->renderTabTitlePanel();
        }

        if ($isGeneralMode)
        {
            $html[] = '<div class="alert alert-danger">' . Translation::get('HomepageInGeneralMode') . '</div>';
        }

        $html[] = $this->renderPackageContainer();
        $html[] = $this->renderContent();

        $html[] =
            '<script src="' . $webPathBuilder->getJavascriptPath('Chamilo\Core\Home') . 'HomeView.js' . '"></script>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    private function getHomeService()
    {
        if (!isset($this->homeService))
        {
            $this->homeService =
                new HomeService(new HomeRepository(), new ElementRightsService(new RightsRepository()));
        }

        return $this->homeService;
    }

    /**
     * Returns whether or not the home viewer is in general mode
     *
     * @return bool
     */
    protected function isGeneralMode()
    {
        if (!isset($this->generalMode))
        {
            $this->generalMode = Session::retrieve('Chamilo\Core\Home\General');
        }

        return $this->generalMode;
    }

    /**
     * @return string
     */
    public function renderButtons()
    {
        $user = $this->get_user();
        $userHomeAllowed = Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_user_home']);
        $generalMode = $this->isGeneralMode();
        $homeUserIdentifier = $this->getHomeService()->determineHomeUserIdentifier($this->get_user());

        $html = [];

        if ($user instanceof User && ($userHomeAllowed || $user->is_platform_admin()))
        {
            $buttonToolBar = new ButtonToolBar();

            if ($userHomeAllowed || $generalMode)
            {
                $splitDropdownButton = new SplitDropdownButton(
                    Translation::get('NewBlock'), new FontAwesomeGlyph('plus'), '#', SubButton::DISPLAY_ICON_AND_LABEL,
                    false, ['portal-add-block btn-link'], null, ['dropdown-menu-right']
                );

                $buttonToolBar->addItem($splitDropdownButton);

                $splitDropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('NewColumn'), null, '#', SubButton::DISPLAY_LABEL, null,
                        ['portal-add-column', 'btn-link']
                    )
                );
                $splitDropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('NewTab'), null, '#', SubButton::DISPLAY_LABEL, null,
                        ['portal-add-tab', 'btn-link']
                    )
                );

                $truncateLink = new Redirect([Manager::PARAM_ACTION => Manager::ACTION_TRUNCATE]);

                if ($homeUserIdentifier != '0')
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('ResetHomepage'), null, $truncateLink->getUrl(), SubButton::DISPLAY_LABEL,
                            Translation::get('ConfirmChosenAction', [], StringUtilities::LIBRARIES),
                            ['portal-reset', 'btn-link']
                        )
                    );
                }
            }

            if (!$generalMode && $user->is_platform_admin())
            {
                $redirect = new Redirect([Manager::PARAM_ACTION => Manager::ACTION_MANAGE_HOME]);

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('ConfigureDefault'), new FontAwesomeGlyph('wrench'), $redirect->getUrl()
                    )
                );
            }
            elseif ($generalMode && $user->is_platform_admin())
            {
                $redirect = new Redirect([Manager::PARAM_ACTION => Manager::ACTION_PERSONAL]);

                $title = $userHomeAllowed ? 'BackToPersonal' : 'ViewDefault';

                if ($splitDropdownButton)
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            Translation::get($title), new FontAwesomeGlyph('home'), $redirect->getUrl(),
                            SubButton::DISPLAY_LABEL
                        )
                    );
                }
                else
                {
                    $buttonToolBar->addItem(
                        new Button(Translation::get($title), new FontAwesomeGlyph('home'), $redirect->getUrl())
                    );
                }
            }

            $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);
            $html[] = '<li class="pull-right portal-actions">' . $buttonToolBarRenderer->render() . '</li>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function renderContent()
    {
        $angularConnectorService = new AngularConnectorService(Configuration::getInstance());
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

        $tabs = $this->getHomeService()->getElements($this->get_user(), Tab::class);

        foreach ($tabs as $tabKey => $tab)
        {
            $tabRenderer = new TabRenderer($this->getApplication(), $this->getHomeService(), $tab);
            $html[] = $tabRenderer->render(
                $this->getHomeService()->isActiveTab($this->getApplication()->getRequest(), $tabKey, $tab)
            );
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function renderPackageContainer()
    {
        $html = [];

        $html[] = '<form class="form-inline package-search">';
        $html[] = '<div class="form-group">';
        $html[] = '<div class="input-group">';

        $glyph = new FontAwesomeGlyph('search', [], null, 'fas');

        $html[] = '<div class="input-group-addon">' . $glyph->render() . '</div>';
        $html[] = '<input type="text" class="form-control" id="portal-package-name" placeholder="' .
            Translation::get('SearchForWidgets') . '">';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<select class="form-control" id="portal-package-context">';
        $html[] = '<option value="">' . Translation::get('AllPackages') . '</option>';
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</form>';

        $html[] = '<div class="row portal-package-blocks">';
        $html[] = '</div>';

        return $this->renderPanel(
            'portal-package-container', 'portal-action portal-package-hide', Translation::get('BrowseBlocks'),
            implode(PHP_EOL, $html)
        );
    }

    public function renderPanel($rowClass, $actionClass, $title, $content)
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

    public function renderTabTitlePanel()
    {
        $html = [];

        $html[] = '<form class="form-inline portal-action-tab-form">';
        $html[] = '<div class="form-group">';
        $html[] = '<input type="text" class="form-control portal-action-tab-title" data-tab-id="" placeholder="' .
            Translation::get('EnterTabTitle') . '" />';
        $html[] = '</div>';

        $html[] = '<button type="submit" class="btn btn-primary portal-tab-title-save">' . Translation::get('Save') .
            '</button>';

        $html[] = '</form>';

        return $this->renderPanel(
            'portal-tab-panel', 'portal-tab-panel-hide', Translation::get('EditTabTitle'), implode(PHP_EOL, $html)
        );
    }

    /**
     * @return string
     */
    public function renderTabs()
    {
        $html = [];

        $html[] = '<ul class="nav nav-tabs portal-nav-tabs">';

        $tabs = $this->getHomeService()->getElements($this->get_user(), Tab::class);

        foreach ($tabs as $tabKey => $tab)
        {
            $tabHeaderRenderer = new TabHeaderRenderer($this->getApplication(), $this->getHomeService(), $tab);
            $html[] = $tabHeaderRenderer->render(
                $this->getHomeService()->isActiveTab($this->getApplication()->getRequest(), $tabKey, $tab)
            );
        }

        $html[] = $this->renderButtons();

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
