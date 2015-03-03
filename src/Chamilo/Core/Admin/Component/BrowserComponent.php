<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Form\AdminSearchForm;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeLinksMenu;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Tabs\DynamicActionsTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class BrowserComponent extends Manager
{
    const PARAM_TAB = 'tab';

    private $tab;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->get_platformadmin())
        {
            throw new NotAllowedException();
        }

        $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        $breadcrumbtrail->truncate(true);
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get('Administration')));
        $breadcrumbtrail->add_help('administration general');

        $this->tab = Request :: get(self :: PARAM_TAB);
        if (! $this->tab)
        {
            $this->tab = 'Chamilo\Core';
        }
        $tab_name = Translation :: get(
            (string) StringUtilities :: getInstance()->createString($this->tab)->upperCamelize());

        $breadcrumbtrail->add(
            new Breadcrumb($this->get_url(array(DynamicTabsRenderer :: PARAM_SELECTED_TAB => $this->tab)), $tab_name));

        $html = array();

        $html[] = $this->render_header();

        $html[] = '<div style="float: left; width: 10%;">';
        $html[] = $this->get_menu();

        $html[] = '</div>';

        $html[] = '<div style="float: right; width: 89%;">';
        $html[] = $this->get_tabs();
        $html[] = '</div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Admin\\', true) .
                 'Resources/Javascript/AdminBrowser.js');
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_menu()
    {
        $menu = new PackageTypeLinksMenu($this->tab, $this->get_url(array(self :: PARAM_TAB => '__TYPE__')));
        return $menu->render_as_tree();
    }

    public function get_tabs()
    {
        $tabs = new DynamicTabsRenderer('admin');

        $packages = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance(PackageList :: MODE_INSTALLED)->get_type_packages();

        $packageNames = array();

        foreach ($packages[$this->tab] as $namespace => $package)
        {
            $packageNames[Translation :: get('TypeName', null, $namespace)] = $package;
        }

        ksort($packageNames);

        foreach ($packageNames as $packageName => $package)
        {
            $managerClass = $package->get_context() . '\Integration\Chamilo\Core\Admin\Manager';

            if (class_exists($managerClass) &&
                 is_subclass_of($managerClass, '\Chamilo\Core\Admin\ActionsSupportInterface'))
            {
                $links = $managerClass :: get_actions();

                $index = 0;
                $index ++;
                $actions_tab = new DynamicActionsTab(
                    ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($package->get_context()),
                    Translation :: get('TypeName', null, $package->get_context()),
                    Theme :: getInstance()->getImagePath($package->get_context(), 'Logo/22'));

                if ($links->get_search())
                {
                    $search_form = new AdminSearchForm($this, $links->get_search(), $index);
                    $actions_tab->add_action(
                        new DynamicAction(
                            null,
                            $search_form->render(),
                            Theme :: getInstance()->getImagePath('Chamilo\Core\Admin', 'Admin/search')));
                }

                foreach ($links->get_links() as $action)
                {
                    $actions_tab->add_action($action);
                }

                $tabs->add_tab($actions_tab);
            }
        }
        return $tabs->render();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('admin_browser');
    }
}
