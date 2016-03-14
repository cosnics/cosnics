<?php
namespace Chamilo\Core\Home\Renderer\Type;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Service\AngularConnectorService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

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
     *
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    private function getHomeService()
    {
        if (! isset($this->homeService))
        {
            $this->homeService = new HomeService(new HomeRepository());
        }

        return $this->homeService;
    }

    /**
     *
     * @param string $type
     * @param integer $parentId
     */
    private function getElements($type, $parentIdentifier = 0)
    {
        if (! isset($this->elements))
        {
            $homeUserIdentifier = $this->determineHomeUserIdentifier();
            $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());

            if ($userHomeAllowed && $this->get_user() instanceof User)
            {
                if ($this->getHomeService()->countElementsByUserIdentifier($homeUserIdentifier) == 0)
                {
                    $this->getHomeService()->createDefaultHomeByUserIdentifier($homeUserIdentifier);
                }
            }

            $elementsResultSet = $this->getHomeService()->getElementsByUserIdentifier($homeUserIdentifier);

            while ($element = $elementsResultSet->next_result())
            {
                $this->elements[$element->get_type()][$element->getParentId()][] = $element;
            }
        }

        if (isset($this->elements[$type]) && isset($this->elements[$type][$parentIdentifier]))
        {
            return $this->elements[$type][$parentIdentifier];
        }
        else
        {
            return array();
        }
    }

    /**
     *
     * @return integer
     */
    private function determineHomeUserIdentifier()
    {
        if (! isset($this->homeUserIdentifier))
        {
            $user = $this->get_user();
            $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());
            $generalMode = \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');

            // Get user id
            if ($user instanceof User && $generalMode && $user->is_platform_admin())
            {
                $this->homeUserIdentifier = 0;
            }
            elseif ($userHomeAllowed && $user instanceof User)
            {
                $this->homeUserIdentifier = $user->get_id();
            }
            else
            {
                $this->homeUserIdentifier = 0;
            }
        }

        return $this->homeUserIdentifier;
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Renderer::render()
     */
    public function render()
    {
        $currentTabIdentifier = $this->getCurrentTabIdentifier();
        $user = $this->get_user();

        $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());
        $generalMode = \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');

        $isEditable = ($user instanceof User && ($userHomeAllowed || ($user->is_platform_admin() && $generalMode)));
        $isGeneralMode = ($generalMode && $user instanceof User && $user->is_platform_admin());

        if ($isGeneralMode)
        {
            $html[] = '<div class="row danger-banner text-danger bg-danger">' .
                 Translation :: get('HomepageInGeneralMode') . '</div>';
        }

        if ($isEditable)
        {
            $html[] = '<script type="text/javascript" src="' .
                 Path :: getInstance()->getJavascriptPath('Chamilo\Core\Home', true) . 'HomeAjax.js' . '"></script>';
        }

        $html[] = $this->renderTabs();

        if ($isEditable)
        {
            $html[] = $this->renderTabTitlePanel();
        }

        $html[] = $this->renderPackageContainer();
        $html[] = $this->renderContent();

        $html[] = '<script type="text/javascript" src="' .
             Path :: getInstance()->getJavascriptPath('Chamilo\Core\Home', true) . 'HomeView.js' . '"></script>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderTabs()
    {
        $tabs = $this->getElements(Tab :: class_name());
        $currentTabIdentifier = $this->getCurrentTabIdentifier();
        $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());
        $generalMode = \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');

        $html = array();

        $html[] = '<ul class="nav nav-tabs portal-nav-tabs">';

        foreach ($tabs as $tabKey => $tab)
        {
            $tab_id = $tab->get_id();

            $listItem = array();

            $listItem[] = '<li';

            if (($tab_id == $currentTabIdentifier) || (count($tabs) == 1) ||
                 (! isset($currentTabIdentifier) && $tabKey == 0))
            {
                $listItem[] = 'class="portal-nav-tab active"';
            }
            else
            {
                $listItem[] = 'class="portal-nav-tab"';
            }

            $listItem[] = ' data-tab-id="' . $tab->get_id() . '"';
            $listItem[] = ' data-tab-title="' . $tab->getTitle() . '"';
            $listItem[] = '>';

            $html[] = implode(' ', $listItem);

            $html[] = '<a class="portal-action-tab-title" href="#">';

            $html[] = '<span class="portal-nav-tab-title">' . htmlspecialchars($tab->getTitle()) . '</span>';

            $isUser = $this->get_user() instanceof User;
            $homeAllowed = $isUser && ($userHomeAllowed || ($this->get_user()->is_platform_admin()) && $generalMode);
            $isAnonymous = $isUser && $this->get_user()->is_anonymous_user();

            if ($isUser && $homeAllowed && ! $isAnonymous)
            {
                $html[] = '<span class="glyphicon glyphicon-remove portal-action-tab-delete ' .
                     (count($tabs) > 1 ? 'show' : 'hidden') . '"></span>';
            }

            $html[] = '</a>';

            $html[] = '</li>';
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
             Translation :: get('EnterTabTitle') . '" />';
        $html[] = '</div>';

        $html[] = '<button type="submit" class="btn btn-primary portal-tab-title-save">' . Translation :: get('Save') .
             '</button>';

        $html[] = '</form>';

        return $this->renderPanel(
            'portal-tab-panel',
            'portal-tab-panel-hide',
            Translation :: get('EditTabTitle'),
            implode(PHP_EOL, $html));
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
             Translation :: get('SearchForWidgets') . '">';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<select class="form-control" id="portal-package-context">';
        $html[] = '<option value="">' . Translation :: get('AllPackages') . '</option>';
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</form>';

        $html[] = '<div class="row portal-package-blocks">';
        $html[] = '</div>';

        return $this->renderPanel(
            'portal-package-container',
            'portal-action portal-package-hide',
            Translation :: get('BrowseBlocks'),
            implode(PHP_EOL, $html));
    }

    /**
     *
     * @return string
     */
    public function renderContent()
    {
        $angularConnectorService = new AngularConnectorService(Configuration :: get_instance());
        $modules = $angularConnectorService->getAngularModules();
        $moduleString = count($modules) > 0 ? '\'' . implode('\', \'', $modules) . '\'' : '';

        $tabs = $this->getElements(Tab :: class_name());
        $currentTabIdentifier = $this->getCurrentTabIdentifier();

        $html = array();

        $html[] = $angularConnectorService->loadAngularModules();

        $html[] = '<script type="text/javascript">';
        $html[] = '(function(){';
        $html[] = '    var homeApp = angular.module(\'homeApp\', [' . $moduleString . ']);';
        $html[] = '})();';
        $html[] = '</script>';

        $html[] = '<div class="portal-tabs" ng-app="homeApp">';

        foreach ($tabs as $tabKey => $tab)
        {
            $isCurrentTab = ((! isset($currentTabIdentifier) && ($tabKey == 0 || count($tabs) == 1)) ||
                 $currentTabIdentifier == $tab->get_id());

            $html[] = '<div class="row portal-tab ' . ($isCurrentTab ? 'show' : 'hidden') . '" data-element-id="' .
                 $tab->get_id() . '">';

            $columns = $this->getElements(Column :: class_name(), $tab->get_id());

            foreach ($columns as $columnKey => $column)
            {
                $html[] = '<div class="col-xs-12 col-md-' . $column->getWidth() . ' portal-column" data-tab-id="' .
                     $tab->get_id() . '" data-element-id="' . $column->get_id() . '" data-element-width="' .
                     $column->getWidth() . '">';

                $blocks = $this->getElements(Block :: class_name(), $column->get_id());

                foreach ($blocks as $block)
                {
                    $blockRendition = BlockRendition :: factory($this, $block);

                    if ($blockRendition->isVisible())
                    {
                        $html[] = $blockRendition->toHtml();
                    }
                }

                $html[] = $this->renderEmptyColumn($column->get_id(), (count($blocks) > 0), (count($columns) == 1));

                $html[] = '</div>';
            }

            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param integer $columnId
     * @param boolean $isEmpty
     * @return string
     */
    public function renderEmptyColumn($columnId, $isEmpty = false, $isOnlyColumn = false)
    {
        $html = array();

        $html[] = '<div class="panel panel-warning portal-column-empty ' . ($isEmpty ? 'hidden' : 'show') . '">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<div class="pull-right">';
        $html[] = '<a href="#" class="portal-action portal-action-column-delete ' . ($isOnlyColumn ? 'hidden' : 'show') .
             '" data-column-id="' . $columnId . '" title="' . Translation :: get('Delete') . '">';
        $html[] = '<span class="glyphicon glyphicon-remove"></span></a>';
        $html[] = '</div>';
        $html[] = '<h3 class="panel-title">' . Translation :: get('EmptyColumnTitle') . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = Translation :: get('EmptyColumnBody');
        $html[] = '</div>';
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
        $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());
        $generalMode = \Chamilo\Libraries\Platform\Session\Session :: retrieve('Chamilo\Core\Home\General');
        $homeUserIdentifier = $this->determineHomeUserIdentifier();

        $html = array();

        if ($user instanceof User && ($userHomeAllowed || $user->is_platform_admin()))
        {
            $buttonToolBar = new ButtonToolBar();

            if ($userHomeAllowed || $generalMode)
            {
                $splitDropdownButton = new SplitDropdownButton(
                    Translation :: get('NewBlock'),
                    new BootstrapGlyph('plus'),
                    '#',
                    SubButton :: DISPLAY_ICON_AND_LABEL,
                    false,
                    'portal-add-block btn-link');
                $splitDropdownButton->setDropdownClasses('dropdown-menu-right');

                $buttonToolBar->addItem($splitDropdownButton);

                $splitDropdownButton->addSubButton(
                    new SubButton(
                        Translation :: get('NewColumn'),
                        null,
                        '#',
                        SubButton :: DISPLAY_LABEL,
                        false,
                        'portal-add-column btn-link'));
                $splitDropdownButton->addSubButton(
                    new SubButton(
                        Translation :: get('NewTab'),
                        null,
                        '#',
                        SubButton :: DISPLAY_LABEL,
                        false,
                        'portal-add-tab btn-link'));

                $redirect = new Redirect(array(Manager :: PARAM_ACTION => Manager :: ACTION_TRUNCATE));

                if ($homeUserIdentifier != '0')
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            Translation :: get('ResetHomepage'),
                            null,
                            '#',
                            SubButton :: DISPLAY_LABEL,
                            true,
                            false,
                            'portal-reset btn-link'));
                }
            }

            if (! $generalMode && $user->is_platform_admin())
            {
                $redirect = new Redirect(array(Manager :: PARAM_ACTION => Manager :: ACTION_MANAGE_HOME));

                $buttonToolBar->addItem(
                    new Button(Translation :: get('ConfigureDefault'), new BootstrapGlyph('wrench'), $redirect->getUrl()));
            }
            elseif ($generalMode && $user->is_platform_admin())
            {
                $redirect = new Redirect(array(Manager :: PARAM_ACTION => Manager :: ACTION_PERSONAL));

                $title = $userHomeAllowed ? 'BackToPersonal' : 'ViewDefault';

                if ($splitDropdownButton)
                {
                    $splitDropdownButton->addSubButton(
                        new SubButton(
                            Translation :: get($title),
                            new BootstrapGlyph('home'),
                            $redirect->getUrl(),
                            SubButton :: DISPLAY_LABEL));
                }
                else
                {
                    $buttonToolBar->addItem(
                        new Button(Translation :: get($title), new BootstrapGlyph('home'), $redirect->getUrl()));
                }
            }

            $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);
            $html[] = '<li class="pull-right portal-actions">' . $buttonToolBarRenderer->render() . '</li>';
        }

        return implode(PHP_EOL, $html);
    }
}
