<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeImportMenu;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\ActionsTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ImporterComponent extends Manager
{
    public const PARAM_TAB = 'tab';

    private $tab;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        $breadcrumbtrail = BreadcrumbTrail::getInstance();
        $breadcrumbtrail->add_help('administration general');

        $this->tab = Request::get(self::PARAM_TAB);

        if (!$this->tab)
        {
            $this->tab = 'Chamilo\Core';
        }

        $tab_name = Translation::get(
            (string) StringUtilities::getInstance()->createString($this->tab)->upperCamelize()
        );

        $breadcrumbtrail->add(
            new Breadcrumb($this->get_url(array(TabsRenderer::PARAM_SELECTED_TAB => $this->tab)), $tab_name)
        );

        $html = [];
        $html[] = $this->render_header();
        $html[] = $this->get_tabs();
        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Admin') . 'AdminBrowser.js'
        );
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('admin_importer');
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function get_menu()
    {
        $menu = new PackageTypeImportMenu($this->tab, $this->get_url(array(self::PARAM_TAB => '__type__')));

        return $menu->render_as_tree();
    }

    public function get_tabs()
    {
        $tabs = new TabsCollection();

        $packages = PlatformPackageBundles::getInstance()->get_type_packages();

        $package_names = [];

        foreach ($packages[$this->tab] as $package)
        {

            $registration = Configuration::is_registered($package->get_context());
            if (!$registration)
            {
                continue;
            }

            $manager_class = $package->get_context() . '\Manager';
            if (!class_exists($manager_class))
            {
                continue;
            }

            $package_names[$package->get_context()] = Translation::get('TypeName', null, $package->get_context());
        }

        asort($package_names);

        foreach ($package_names as $package => $package_name)
        {
            $isRegistered = Configuration::is_registered($package);

            if (!$isRegistered)
            {
                continue;
            }

            $manager_class = $package . '\Integration\Chamilo\Core\Admin\Manager';

            if (class_exists($manager_class) &&
                is_subclass_of($manager_class, 'Chamilo\Core\Admin\ImportActionsInterface', true))
            {
                $links = $manager_class::get_import_actions();

                $index = 0;

                $glyph = new NamespaceIdentGlyph(
                    $package, true, false, false, IdentGlyph::SIZE_SMALL, []
                );

                $index ++;
                $actions_tab = new ActionsTab(
                    ClassnameUtilities::getInstance()->getPackageNameFromNamespace($package),
                    Translation::get('TypeName', null, $package), $glyph
                );

                foreach ($links as $action)
                {
                    $actions_tab->addAction($action);
                }

                $tabs->add($actions_tab);
            }
        }

        return $this->getTabsRenderer()->render('admin', $tabs);
    }

    public function has_menu()
    {
        return true;
    }
}
