<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingsParser;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\ConfigurationFormType;
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
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use stdClass;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigureComponent extends ProfileComponent
{
    public const string PARAM_SELECTED_CONTEXT = 'selected_context';

    protected string $selectedContext;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        FormFactoryInterface $formFactory, TabsRenderer $tabsRenderer, Environment $twigEnvironment,
        bool $userCanChangePicture, ?UserPictureProviderInterface $userPictureProvider,
        protected readonly ConfigurationFormType $configurationFormType,
        protected readonly PackageBundlesCacheService $packageBundlesCacheService,
        protected readonly ParameterBagInterface $platformParameterBag,
        protected readonly SystemPathBuilder $systemPathBuilder,
        protected readonly UserSettingsParser $userSettingsParser,
        protected readonly UserSettingsService $userSettingsService
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService, $formFactory,
            $tabsRenderer, $twigEnvironment, $userCanChangePicture, $userPictureProvider
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');

        $configureUri = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::CONFIGURE->value,
                self::PARAM_SELECTED_CONTEXT => $this->getSelectedContext()
            ]
        );

        $form = $this->formFactory->create(
            ConfigurationFormType::class, $this->getFormData($currentUser), [
                'action' => $configureUri,
                'context' => $this->getSelectedContext()
            ]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = $this->processData($form->getData());

            $success = $this->getUserSettingsService()->updateUserSettingsFromParameters(
                $currentUser, $this->getSelectedContext(), $submittedData, $currentUser
            );

            $this->alertsManager->addAlert(
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
            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $this->getContent($form);
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }

    public function getConfigurationFormType(): ConfigurationFormType
    {
        return $this->configurationFormType;
    }

    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function getContent($form): string
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

        $html[] = $this->tabsRenderer->renderNavigation('package', $tabs, $this->getSelectedContext());
        $html[] = $this->twigEnvironment->render('form.html.twig', [
            'form' => $form->createView(),
        ]);

        return implode(PHP_EOL, $html);
    }

    public function getFormData(User $user): array
    {
        $platformParameters = $this->getPlatformParameterBag();
        $data = [];

        $configuration =
            $this->getUserSettingsParser()->determineConfigurablePackageContextSettings($this->getSelectedContext());

        foreach ($configuration as $settings) {
            foreach ($settings as $name => $setting) {
                $fieldName = str_replace('.', '-', $name);

                $configurationValue = $this->getUserSettingsService()->findUserSetting($user, $name);

                if ($setting['field'] == CheckboxType::class) {
                    $dataValue = (bool) $configurationValue;
                }
                elseif (isset($configurationValue) && ($configurationValue == 0 || !empty($configurationValue))) {
                    $dataValue = $configurationValue;
                }
                elseif ($platformParameters->has($name)) {
                    $dataValue = $platformParameters->get($name);
                }
                else {
                    $dataValue = $setting['default'];
                }

                if (in_array($setting['field'], [ChoiceType::class, RadioType::class])) {
                    $dataObject = new stdClass();
                    $dataObject->value = $dataValue;
                    $data[$fieldName] = $dataObject;
                }
                else {
                    $data[$fieldName] = $dataValue;
                }
            }
        }

        return $data;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->packageBundlesCacheService;
    }

    public function getPlatformParameterBag(): ParameterBagInterface
    {
        return $this->platformParameterBag;
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

    public function getUserSettingsParser(): UserSettingsParser
    {
        return $this->userSettingsParser;
    }

    public function getUserSettingsService(): UserSettingsService
    {
        return $this->userSettingsService;
    }

    protected function processData(array $originalData): array
    {
        $data = [];

        $configuration =
            $this->getUserSettingsParser()->determineConfigurablePackageContextSettings($this->getSelectedContext());

        foreach ($configuration as $settings) {
            foreach ($settings as $name => $setting) {
                $fieldName = str_replace('.', '-', $name);

                if (in_array($setting['field'], [ChoiceType::class, RadioType::class])) {
                    $data[$name] = $originalData[$fieldName]->value;
                }
                else {
                    $data[$name] = $originalData[$fieldName];
                }
            }
        }

        return $data;
    }
}
