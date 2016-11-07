<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Form\ConfigurationForm;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeSettingsMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: configurer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 *
 * @package admin.lib.admin_manager.component
 */
/**
 * Admin component
 */
class ConfigurerComponent extends Manager
{
    const PARAM_TAB = 'tab';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->set_parameter(self :: PARAM_CONTEXT, $this->get_context());

        $context = $this->get_context();

        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        $form = new ConfigurationForm(
            $this->get_context(),
            'config',
            'post',
            $this->get_url(array(self :: PARAM_CONTEXT => $this->get_context(), self :: PARAM_TAB => $this->get_tab())));

        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirect(
                Translation :: get(
                    $success ? 'ObjectUpdated' : 'ObjectNotUpdated',
                    array('OBJECT' => Translation :: get('Setting')),
                    Utilities :: COMMON_LIBRARIES),
                ($success ? false : true),
                array(
                    Application :: PARAM_ACTION => self :: ACTION_CONFIGURE_PLATFORM,
                    self :: PARAM_CONTEXT => $this->get_context(),
                    DynamicVisualTabsRenderer :: PARAM_SELECTED_TAB => $this->get_tab()));
        }
        else
        {
            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb(
                    $this->get_url(array(DynamicVisualTabsRenderer :: PARAM_SELECTED_TAB => $this->get_context())),
                    Translation :: get('TypeName', null, $this->get_context())));

            $html = array();

            $html[] = $this->render_header();
            $html[] = '<div style="float: left; width: 15%;">';
            $html[] = $this->get_menu();
            $html[] = '</div>';
            $html[] = '<div style="float: right; width: 84%;">';

            $packages = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance()->get_type_packages();

            foreach ($packages[$this->get_tab()] as $package)
            {
                if (\Chamilo\Configuration\Configuration :: get_instance()->has_settings($package->get_context()))
                {
                    $package_names[$package->get_context()] = Translation :: get(
                        'TypeName',
                        null,
                        $package->get_context());
                }
            }

            asort($package_names);

            $tabs = new DynamicVisualTabsRenderer('settings', $form->toHtml());
            foreach ($package_names as $package => $package_name)
            {
                if (\Chamilo\Configuration\Configuration :: get_instance()->has_settings($package))
                {
                    $tabs->add_tab(
                        new DynamicVisualTab(
                            $package,
                            Translation :: get('TypeName', null, $package),
                            Theme :: getInstance()->getImagePath($package, 'Logo/22'),
                            $this->get_url(
                                array(self :: PARAM_TAB => $this->get_tab(), self :: PARAM_CONTEXT => $package)),
                            $this->get_context() == $package));
                }
            }

            $html[] = $tabs->render();
            $html[] = '</div>';

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_menu()
    {
        $menu = new PackageTypeSettingsMenu(
            $this->get_tab(),
            $this->get_url(array(self :: PARAM_TAB => '__TYPE__', self :: PARAM_CONTEXT => null)));
        return $menu->render_as_tree();
    }

    public function get_context()
    {
        $context = Request :: get(self :: PARAM_CONTEXT);
        if (! isset($context))
        {
            $packages = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance()->get_type_packages();

            foreach ($packages[$this->get_tab()] as $package)
            {
                if (\Chamilo\Configuration\Configuration :: get_instance()->has_settings($package->get_context()))
                {
                    $package_names[$package->get_context()] = Translation :: get(
                        'TypeName',
                        null,
                        $package->get_context());
                }
            }

            asort($package_names);

            $package_names = array_keys($package_names);

            return $package_names[0];
        }
        else
        {
            return $context;
        }
    }

    public function get_tab()
    {
        $tab = Request :: get(self :: PARAM_TAB);
        if (! isset($tab))
        {
            return 'Chamilo\Core';
        }
        else
        {
            return $tab;
        }
    }
}
