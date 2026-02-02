<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\UserInterface\Form\ConfigurationForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\IdentGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\GenericTabsRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\LinkTabsRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Admin\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigureComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     */
    public function run(): Response
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $form = new ConfigurationForm(
            $this->getSelectedContext(), 'config', FormValidator::FORM_METHOD_POST,
            $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => self::ACTION_CONFIGURE,
                    self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
                ]
            )
        );

        if ($form->validate()) {
            $success = $form->updateConfiguration();

            return $this->redirectWithMessage(
                $translator->trans(
                    $success ? 'ObjectUpdated' : 'ObjectNotUpdated', ['OBJECT' => $translator->trans('Setting')],
                    StringUtilities::LIBRARIES
                ), !$success, [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => self::ACTION_CONFIGURE,
                    self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
                ]
            );
        }
        else {
            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    $this->getUrlGenerator()->fromParameters([
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => self::ACTION_CONFIGURE,
                        self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext(),
                        GenericTabsRenderer::PARAM_SELECTED_TAB => $this->getSelectedContext()
                    ]), $translator->trans('TypeName', [], $this->getSelectedContext())
                )
            );

            $packages = $this->getPackageBundlesCacheService()->getPackages();

            foreach ($packages as $package) {
                if ($this->getConfigurationConsulter()->hasSettingsForContext($package->getContext())) {
                    $packageNames[$package->getContext()] = $translator->trans(
                        'TypeName', [], $package->getContext()
                    );
                }
            }

            asort($packageNames);

            $tabs = new TabsCollection();

            foreach ($packageNames as $package => $packageName) {
                if ($this->getConfigurationConsulter()->hasSettingsForContext($package)) {
                    $tabs->add(
                        new LinkTab(
                            $package, $translator->trans('TypeName', [], $package), new NamespaceIdentGlyph(
                            $package, true, false, false, IdentGlyph::SIZE_SMALL
                        ), $this->getUrlGenerator()->fromParameters([
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Application::PARAM_ACTION => self::ACTION_CONFIGURE,
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
