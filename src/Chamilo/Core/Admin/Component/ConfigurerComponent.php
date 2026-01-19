<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Configuration\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\UserInterface\Form\ConfigurationForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurerComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run(): Response
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $form = new ConfigurationForm(
            $this->getSelectedContext(), 'config', FormValidator::FORM_METHOD_POST,
            $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => self::ACTION_CONFIGURE_PLATFORM,
                    self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
                ]
            )
        );

        if ($form->validate())
        {
            $success = $form->update_configuration();

            return $this->redirectWithMessage(
                $translator->trans(
                    $success ? 'ObjectUpdated' : 'ObjectNotUpdated', ['OBJECT' => $translator->trans('Setting')],
                    StringUtilities::LIBRARIES
                ), !$success, [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => self::ACTION_CONFIGURE_PLATFORM,
                    self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
                ]
            );
        }
        else
        {
            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    $this->getUrlGenerator()->fromParameters([
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => self::ACTION_CONFIGURE_PLATFORM,
                        self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext(),
                        GenericTabsRenderer::PARAM_SELECTED_TAB => $this->getSelectedContext()
                    ]), $translator->trans('TypeName', [], $this->getContext())
                )
            );

            $packages = $this->getPackageBundlesCacheService()->getPackages();

            foreach ($packages as $package)
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
                        ), $this->getUrlGenerator()->fromParameters([
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Application::PARAM_ACTION => self::ACTION_CONFIGURE_PLATFORM,
                            self::PARAM_SELECTED_CONTEXT => $package
                        ]), $this->getSelectedContext() == $package
                        )
                    );
                }
            }

            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $this->getLinkTabsRenderer()->render($tabs, $form->render());
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
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

    protected function getSelectedContext(): ?string
    {
        return $this->getRequest()->query->get(self::PARAM_SELECTED_CONTEXT, 'Chamilo\Core\Admin');
    }
}
