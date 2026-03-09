<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\ConfigurationForm;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigureComponent extends ProfileComponent
{
    public const PARAM_SELECTED_CONTEXT = 'context';

    private ConfigurationForm $form;

    private string $selectedContext;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');

        $this->form = new ConfigurationForm(
            $this->getSelectedContext(), 'config', FormValidator::FORM_METHOD_POST,
            $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()]
            ), $currentUser
        );

        if ($this->form->validate()) {
            $success = $this->form->updateUserSettings();

            return $this->redirectWithMessage(
                $this->getTranslator()->trans($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), !$success,
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => ActionEnum::CONFIGURE->value,
                    self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
                ]
            );
        }
        else {
            return new Response($this->renderPage($currentUser));
        }
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(User $user): string
    {
        $translator = $this->getTranslator();
        $tabs = new TabsCollection();

        $packages = $this->getPackageBundlesCacheService()->getPackages();
        $packageNames = [];

        foreach ($packages as $package) {
            $packageContext = $package->getContext();
            $file =
                $this->getSystemPathBuilder()->namespaceToFullPath($packageContext) . 'Resources/Settings/settings.xml';

            if (file_exists($file)) {
                $packageNames[$packageContext] = $translator->trans('TypeName', [], $packageContext);
            }
        }

        asort($packageNames);

        foreach ($packageNames as $package => $packageName) {
            $packageUrl = $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => ActionEnum::CONFIGURE->value,
                    self::PARAM_SELECTED_CONTEXT => $package
                ]
            );

            $isCurrentTab = ($this->getSelectedContext() === $package);

            $tab = new LinkTab(
                $package, $translator->trans('TypeName', [], $package), new NamespaceIdentGlyph(
                $package, true
            ), $packageUrl, $isCurrentTab
            );

            $tabs->add($tab);
        }

        $html = [];

        $html[] = $this->getTabsRenderer()->renderNavigation('package', $tabs, $this->getSelectedContext());
        $html[] = $this->form->render();

        return implode(PHP_EOL, $html);
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }

    public function getSelectedContext(): ?string
    {
        if (!isset($this->selectedContext)) {
            $this->selectedContext =
                $this->getRequest()->query->get(self::PARAM_SELECTED_CONTEXT, StringUtilities::LIBRARIES);
        }

        return $this->selectedContext;
    }
}
