<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Form\ConfigurationForm;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Menu\PackageTypeSettingsMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\MenuComponentInterface;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Admin\Component
 */
class ConfigurerComponent extends Manager implements MenuComponentInterface
{
    public const PARAM_TAB = 'tab';

    /**
     * Runs this component and displays its output.
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $this->set_parameter(self::PARAM_CONTEXT, $this->getContext());

        $this->checkAuthorization(Manager::CONTEXT, 'ManageChamilo');

        $form = new ConfigurationForm(
            $this->getContext(), 'config', FormValidator::FORM_METHOD_POST,
            $this->get_url([self::PARAM_CONTEXT => $this->getContext(), self::PARAM_TAB => $this->getTab()])
        );

        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirectWithMessage(
                $translator->trans(
                    $success ? 'ObjectUpdated' : 'ObjectNotUpdated', ['OBJECT' => $translator->trans('Setting')],
                    StringUtilities::LIBRARIES
                ), !$success, [
                    Application::PARAM_ACTION => self::ACTION_CONFIGURE_PLATFORM,
                    self::PARAM_CONTEXT => $this->getContext(),
                    GenericTabsRenderer::PARAM_SELECTED_TAB => $this->getTab()
                ]
            );
        }
        else
        {
            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    $this->get_url([GenericTabsRenderer::PARAM_SELECTED_TAB => $this->getContext()]),
                    $translator->trans('TypeName', [], $this->getContext())
                )
            );

            $packages = $this->getPackageBundlesCacheService()->getAllPackages()->getNestedTypedPackages();

            foreach ($packages[$this->getTab()] as $package)
            {
                if ($this->getConfigurationConsulter()->hasSettingsForContext($package->get_context()))
                {
                    $package_names[$package->get_context()] = $translator->trans(
                        'TypeName', [], $package->get_context()
                    );
                }
            }

            asort($package_names);

            $tabs = new TabsCollection();

            foreach ($package_names as $package => $package_name)
            {
                if ($this->getConfigurationConsulter()->hasSettingsForContext($package))
                {
                    $tabs->add(
                        new LinkTab(
                            $package, $translator->trans('TypeName', [], $package), new NamespaceIdentGlyph(
                            $package, true, false, false, IdentGlyph::SIZE_SMALL
                        ), $this->get_url([self::PARAM_TAB => $this->getTab(), self::PARAM_CONTEXT => $package]),
                            $this->getContext() == $package
                        )
                    );
                }
            }

            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->getLinkTabsRenderer()->render($tabs, $form->render());
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getContext(): string
    {
        $context = $this->getRequest()->query->get(self::PARAM_CONTEXT);

        if (!isset($context))
        {
            $packages = $this->getPackageBundlesCacheService()->getAllPackages()->getNestedTypedPackages();

            foreach ($packages[$this->getTab()] as $package)
            {
                if ($this->getConfigurationConsulter()->hasSettingsForContext($package->get_context()))
                {
                    $packageNames[$package->get_context()] = $this->getTranslator()->trans(
                        'TypeName', [], $package->get_context()
                    );
                }
            }

            asort($packageNames);

            $packageNames = array_keys($packageNames);

            return $packageNames[0];
        }
        else
        {
            return $context;
        }
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }

    public function getTab(): string
    {
        return $this->getRequest()->query->get(self::PARAM_TAB, 'Chamilo\Core');
    }

    /**
     * @throws \ReflectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderApplicationMenu(): string
    {
        $menu = new PackageTypeSettingsMenu(
            $this->getClassnameUtilities(), $this->getConfigurationConsulter(), $this->getPackageBundlesCacheService(),
            $this->getTab(), $this->get_url([self::PARAM_TAB => '__TYPE__', self::PARAM_CONTEXT => null])
        );

        return $menu->render_as_tree();
    }
}
