<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Core\Admin\Form\ConfigurationForm;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeSettingsMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package admin.lib.admin_manager.component
 */

/**
 * Admin component
 */
class ConfigurerComponent extends Manager
{
    public const PARAM_TAB = 'tab';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->set_parameter(self::PARAM_CONTEXT, $this->get_context());

        $context = $this->get_context();

        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        $form = new ConfigurationForm(
            $this->get_context(), 'config', FormValidator::FORM_METHOD_POST,
            $this->get_url(array(self::PARAM_CONTEXT => $this->get_context(), self::PARAM_TAB => $this->get_tab()))
        );

        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirectWithMessage(
                Translation::get(
                    $success ? 'ObjectUpdated' : 'ObjectNotUpdated', array('OBJECT' => Translation::get('Setting')),
                    StringUtilities::LIBRARIES
                ), !$success, array(
                    Application::PARAM_ACTION => self::ACTION_CONFIGURE_PLATFORM,
                    self::PARAM_CONTEXT => $this->get_context(),
                    GenericTabsRenderer::PARAM_SELECTED_TAB => $this->get_tab()
                )
            );
        }
        else
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(array(GenericTabsRenderer::PARAM_SELECTED_TAB => $this->get_context())),
                    Translation::get('TypeName', null, $this->get_context())
                )
            );

            $packages = PlatformPackageBundles::getInstance()->get_type_packages();

            foreach ($packages[$this->get_tab()] as $package)
            {
                if (Configuration::getInstance()->has_settings($package->get_context()))
                {
                    $package_names[$package->get_context()] = Translation::get(
                        'TypeName', null, $package->get_context()
                    );
                }
            }

            asort($package_names);

            $tabs = new TabsCollection();

            foreach ($package_names as $package => $package_name)
            {
                if (Configuration::getInstance()->has_settings($package))
                {
                    $tabs->add(
                        new LinkTab(
                            $package, Translation::get('TypeName', null, $package), new NamespaceIdentGlyph(
                            $package, true, false, false, IdentGlyph::SIZE_SMALL
                        ), $this->get_url(array(self::PARAM_TAB => $this->get_tab(), self::PARAM_CONTEXT => $package)),
                            $this->get_context() == $package
                        )
                    );
                }
            }

            $html = [];

            $html[] = $this->render_header();
            $html[] = $this->getLinkTabsRenderer()->render($tabs, $form->render());
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function get_context()
    {
        $context = Request::get(self::PARAM_CONTEXT);
        if (!isset($context))
        {
            $packages = PlatformPackageBundles::getInstance()->get_type_packages();

            foreach ($packages[$this->get_tab()] as $package)
            {
                if (Configuration::getInstance()->has_settings($package->get_context()))
                {
                    $package_names[$package->get_context()] = Translation::get(
                        'TypeName', null, $package->get_context()
                    );
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

    public function get_menu(): string
    {
        $menu = new PackageTypeSettingsMenu(
            $this->get_tab(), $this->get_url(array(self::PARAM_TAB => '__TYPE__', self::PARAM_CONTEXT => null))
        );

        return $menu->render_as_tree();
    }

    public function get_tab()
    {
        $tab = Request::get(self::PARAM_TAB);
        if (!isset($tab))
        {
            return 'Chamilo\Core';
        }
        else
        {
            return $tab;
        }
    }

    /**
     * @return bool
     */
    public function has_menu(): bool
    {
        return true;
    }
}
