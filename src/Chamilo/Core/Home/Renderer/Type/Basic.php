<?php

namespace Chamilo\Core\Home\Renderer\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
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
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Home\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Basic extends Renderer
{

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Element[]
     */
    private $elements;

    /**
     *
     * @var integer
     */
    private $homeUserIdentifier;

    /**
     *
     * @var \Chamilo\Core\Home\Service\HomeService
     */
    private $homeService;

    /**
     * Caching variable to check if the home page is in general mode
     *
     * @var boolean
     */
    protected $generalMode;

    /**
     *
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    private function getHomeService()
    {
        if (!isset($this->homeService))
        {
            $this->homeService = new HomeService(
                new HomeRepository(),
                new ElementRightsService(new RightsRepository($this->getGroupSubscriptionService()))
            );
        }

        return $this->homeService;
    }

    /**
     * @return GroupSubscriptionService
     */
    protected function getGroupSubscriptionService()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        return $container->get(GroupSubscriptionService::class);
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Renderer::render()
     */
    public function render()
    {
        $user = $this->get_user();

        $userHomeAllowed = Configuration::getInstance()->get_setting(array(Manager::context(), 'allow_user_home'));
        $generalMode = $this->isGeneralMode();

        $isEditable = ($user instanceof User && ($userHomeAllowed || ($user->is_platform_admin() && $generalMode)));
        $isGeneralMode = ($generalMode && $user instanceof User && $user->is_platform_admin());

        if ($isEditable)
        {
            $html[] = '<script type="text/javascript" src="' .
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Home', true) . 'HomeAjax.js' . '"></script>';
        }

        if ($this->isGeneralMode())
        {
            $html[] = '<script type="text/javascript" src="' .
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Home', true) . 'HomeGeneralModeAjax.js' .
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

        $html[] = '<script type="text/javascript" src="' .
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Home', true) . 'HomeView.js' . '"></script>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderTabs()
    {
        $html = array();

        $html[] = '<ul class="nav nav-tabs portal-nav-tabs">';

        $tabs = $this->getHomeService()->getElements($this->get_user(), Tab::class_name());

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

    public function renderPanel($rowClass, $actionClass, $title, $content)
    {
        $html = array();

        $html[] = '<div class="row ' . $rowClass . ' hidden">';

        $html[] = '<div class="col-xs-12">';
        $html[] = '<div class="panel panel-primary">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<div class="pull-right">';
        $html[] = '<a href="#" class="' . $actionClass . '"><span class="glyphicon glyphicon-remove"></span></a>';
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
        $html = array();

        $html[] = '<form class="form-inline portal-action-tab-form">';
        $html[] = '<div class="form-group">';
        $html[] = '<input type="text" class="form-control portal-action-tab-title" data-tab-id="" placeholder="' .
            Translation::get('EnterTabTitle') . '" />';
        $html[] = '</div>';

        $html[] = '<button type="submit" class="btn btn-primary portal-tab-title-save">' . Translation::get('Save') .
            '</button>';

        $html[] = '</form>';

        return $this->renderPanel(
            'portal-tab-panel',
            'portal-tab-panel-hide',
            Translation::get('EditTabTitle'),
            implode(PHP_EOL, $html)
        );
    }

    /**
     *
     * @return string
     */
    public function renderPackageContainer()
    {
        $html = array();

        $html[] = '<form class="form-inline package-search">';
        $html[] = '<div class="form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon"><span class="glyphicon glyphicon-search"></span></div>';
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
            'portal-package-container',
            'portal-action portal-package-hide',
            Translation::get('BrowseBlocks'),
            implode(PHP_EOL, $html)
        );
    }

    /**
     *
     * @return string
     */
    public function renderContent()
    {
        $angularConnectorService = new AngularConnectorService(Configuration::getInstance());
        $modules = $angularConnectorService->getAngularModules();
        $moduleString = count($modules) > 0 ? '\'' . implode('\', \'', $modules) . '\'' : '';

        $html = array();

        $html[] = $angularConnectorService->loadAngularModules();

        $html[] = '<script type="text/javascript">';
        $html[] = '(function(){';
        $html[] = '    var homeApp = angular.module(\'homeApp\', [' . $moduleString . ']);';
        $html[] = '    homeApp.filter(\'arrayToString\', function() { return function(x) { return x; }; });';
        $html[] = '})();';
        $html[] = '</script>';

        $html[] = '<div class="portal-tabs" ng-app="homeApp">';

        $tabs = $this->getHomeService()->getElements($this->get_user(), Tab::class_name());

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
     *
     * @return string
     */
    public function renderButtons()
    {
        $user = $this->get_user();
        $userHomeAllowed = Configuration::getInstance()->get_setting(array(Manager::context(), 'allow_user_home'));
        $generalMode = $this->isGeneralMode();
        $homeUserIdentifier = $this->getHomeService()->determineHomeUserIdentifier($this->get_user());

        $html = array();

        if ($user instanceof User && ($userHomeAllowed || $user->is_platform_admin()))
        {
            $buttonToolBar = new ButtonToolBar();

            if ($userHomeAllowed || $generalMode)
            {
                $splitDropdownButton = new SplitDropdownButton(
                    Translation::get('NewBlock'),
                    new FontAwesomeGlyph('plus'),
                    '#',
                    SubButton::DISPLAY_ICON_AND_LABEL,
                    false,
                    'portal-add-block btn-link'
                );
                $splitDropdownButton->setDropdownClasses('dropdown-menu-right');

                $buttonToolBar->addItem($splitDropdownButton);

                $splitDropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('NewColumn'),
                        null,
                        '#',
                        SubButton::DISPLAY_LABEL,
                        false,
                        'portal-add-column btn-link'
                    )
                );
                $splitDropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('NewTab'),
                        null,
                        '#',
                        SubButton::DISPLAY_LABEL,
                        false,
                        'portal-add-tab btn-link'
                    )
                );

                $truncateLink = new Redirect(array(Manager::PARAM_ACTION => Manager::ACTION_TRUNCATE));

                if ($homeUserIdentifier != '0')
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('ResetHomepage'),
                            null,
                            $truncateLink->getUrl(),
                            SubButton::DISPLAY_LABEL,
                            true,
                            'portal-reset btn-link'
                        )
                    );
                }
            }

            if (!$generalMode && $user->is_platform_admin())
            {
                $redirect = new Redirect(array(Manager::PARAM_ACTION => Manager::ACTION_MANAGE_HOME));

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('ConfigureDefault'), new FontAwesomeGlyph('wrench'), $redirect->getUrl()
                    )
                );
            }
            elseif ($generalMode && $user->is_platform_admin())
            {
                $redirect = new Redirect(array(Manager::PARAM_ACTION => Manager::ACTION_PERSONAL));

                $title = $userHomeAllowed ? 'BackToPersonal' : 'ViewDefault';

                if ($splitDropdownButton)
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            Translation::get($title),
                            new FontAwesomeGlyph('home'),
                            $redirect->getUrl(),
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
     * Returns whether or not the home viewer is in general mode
     *
     * @return bool
     */
    protected function isGeneralMode()
    {
        if (!isset($this->generalMode))
        {
            $this->generalMode = \Chamilo\Libraries\Platform\Session\Session::retrieve('Chamilo\Core\Home\General');
        }

        return $this->generalMode;
    }
}
