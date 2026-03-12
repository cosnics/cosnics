<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\ConfigurationForm;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigureComponent extends ProfileComponent
{
    public const string PARAM_SELECTED_CONTEXT = 'selected_context';

    protected PackageBundlesCacheService $packageBundlesCacheService;

    protected SystemPathBuilder $systemPathBuilder;

    private ConfigurationForm $form;

    private string $selectedContext;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, SystemPathBuilder $systemPathBuilder,
        UserService $userService, UrlGenerator $urlGenerator, PackageBundlesCacheService $packageBundlesCacheService,
        TabsRenderer $tabsRenderer, bool $userCanChangePicture
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator, $tabsRenderer,
            $userCanChangePicture
        );

        $this->systemPathBuilder = $systemPathBuilder;
        $this->packageBundlesCacheService = $packageBundlesCacheService;
    }

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

            $this->getAlertsManager()->addAlert(
                new Alert(
                    $this->getTranslator()->trans($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'),
                    !$success ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::CONFIGURE->value,
                self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
            ]));
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
                    ApplicationInterface::PARAM_ACTION => ActionEnum::CONFIGURE->value,
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
        return $this->packageBundlesCacheService;
    }

    public function getSelectedContext(): ?string
    {
        if (!isset($this->selectedContext)) {
            $this->selectedContext =
                $this->getRequest()->query->get(self::PARAM_SELECTED_CONTEXT, StringUtilities::LIBRARIES);
        }

        return $this->selectedContext;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }
}
