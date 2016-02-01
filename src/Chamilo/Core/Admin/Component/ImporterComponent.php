<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeImportMenu;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicActionsTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ImporterComponent extends Manager
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
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Admin') . 'AdminBrowser.js');
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_menu()
    {
        $menu = new PackageTypeImportMenu($this->tab, $this->get_url(array(self :: PARAM_TAB => '__type__')));
        return $menu->render_as_tree();
    }

    public function get_tabs()
    {
        $tabs = new DynamicTabsRenderer('admin');

        $packages = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance()->get_type_packages();

        $package_names = array();

        foreach ($packages[$this->tab] as $package)
        {

            $registration = \Chamilo\Configuration\Configuration :: is_registered($package->get_context());
            if (! $registration)
            {
                continue;
            }

            $manager_class = $package->get_context() . '\Manager';
            if (! class_exists($manager_class))
            {
                continue;
            }

            $package_names[$package->get_context()] = Translation :: get('TypeName', null, $package->get_context());
        }

        asort($package_names);

        foreach ($package_names as $package => $package_name)
        {
            $isRegistered = \Chamilo\Configuration\Configuration :: is_registered($package);

            if (! $isRegistered)
            {
                continue;
            }

            $manager_class = $package . '\Integration\Chamilo\Core\Admin\Manager';

            if (class_exists($manager_class) &&
                 is_subclass_of($manager_class, 'Chamilo\Core\Admin\ImportActionsInterface', true))
            {
                $links = $manager_class :: get_import_actions();

                $index = 0;

                $index ++;
                $actions_tab = new DynamicActionsTab(
                    ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($package),
                    Translation :: get('TypeName', null, $package),
                    Theme :: getInstance()->getImagePath($package, 'Logo/22'));

                foreach ($links as $action)
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
        $breadcrumbtrail->add_help('admin_importer');
    }
}
