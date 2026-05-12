<?php
namespace Chamilo\Core\Menu\Implementation\Menu;

use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\Menu\Architecture\Interface\ConfigurableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\SelectableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Trait\TranslatableItemTrait;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Enum\IdentGlyphSizeEnum;
use stdClass;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Implementation\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class ApplicationItemRenderer extends ItemRenderer
    implements SelectableItemInterface, TranslatableItemInterface, ConfigurableItemInterface
{
    use TranslatableItemTrait;

    public const string CONFIGURATION_APPLICATION = 'application';
    public const string CONFIGURATION_COMPONENT = 'component';
    public const string CONFIGURATION_EXTRA_PARAMETERS = 'extra_parameters';
    public const string CONFIGURATION_USE_TRANSLATION = 'use_translation';

    public function __construct(
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        protected PackageBundlesCacheService $packageBundlesCacheService, protected UrlGenerator $urlGenerator,
        protected FormTypeBuilder $formTypeBuilder, protected array $fallbackIsoCodes
    )
    {
        parent::__construct($translator, $itemCacheService, $request);
    }

    public function render(Item $item, User $user): string
    {
        $html = [];

        $html[] = '<li class="nav-item">';

        $title = $this->renderTitleForCurrentLanguage($item);

        $html[] = '<a class="text-center nav-link' . ($this->isSelected($item, $user) ? ' active' : '') . '" href="' .
            $this->getApplicationItemUrl($item) . '">';

        if ($item->showIcon()) {
            if (!empty($item->getIconClass())) {
                $glyph = new FontAwesomeGlyph($item->getIconClass(), ['fa-2x'], $title, 'fas');
            }
            else {
                $glyph = new NamespaceIdentGlyph(
                    $item->getSetting(self::CONFIGURATION_APPLICATION), false, false, false, IdentGlyphSizeEnum::SMALL,
                    [], $title
                );
            }

            $html[] = $glyph->render();
        }

        if ($item->showTitle()) {
            $html[] = '<div>' . $title . '</div>';
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function addConfigurationToForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilder = $this->formTypeBuilder;

        $builder->add(
            $formTypeBuilder->createCategory(
                $builder, 'category_properties', $this->translator->trans('Properties', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $formTypeBuilder->createSelect(
                $builder, self::CONFIGURATION_APPLICATION,
                $this->translator->trans('Application', [], Manager::CONTEXT), true, $this->getApplicationOptions()
            )
        );

        $builder->add(
            $formTypeBuilder->createCheckbox(
                $builder, self::CONFIGURATION_USE_TRANSLATION,
                $this->translator->trans('UseTranslation', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $formTypeBuilder->createText(
                $builder, self::CONFIGURATION_COMPONENT, $this->translator->trans('Component', [], Manager::CONTEXT),
                false
            )
        );

        $builder->add(
            $formTypeBuilder->createText(
                $builder, self::CONFIGURATION_EXTRA_PARAMETERS,
                $this->translator->trans('ExtraParameters', [], Manager::CONTEXT), false
            )
        );
    }

    protected function getApplicationItemUrl(Item $item): string
    {
        $application = $item->getSetting(self::CONFIGURATION_APPLICATION);

        if ($application == 'root') {
            return $this->urlGenerator->fromParameters();
        }

        $parameters = [];

        $parameters[ApplicationInterface::PARAM_CONTEXT] = $application;

        $component = $item->getSetting(self::CONFIGURATION_COMPONENT);

        if ($component) {
            $parameters[ApplicationInterface::PARAM_ACTION] = $component;
        }

        $extraParameters = $item->getSetting(self::CONFIGURATION_EXTRA_PARAMETERS);

        if ($extraParameters) {
            parse_str($extraParameters, $parsedExtraParameters);

            foreach ($parsedExtraParameters as $key => $value) {
                $parameters[$key] = $value;
            }
        }

        return $this->urlGenerator->fromParameters($parameters);
    }

    /**
     * @return \stdClass[]
     */
    protected function getApplicationOptions(): array
    {
        $packages = $this->packageBundlesCacheService->getPackages();

        $activeApplications = [];

        foreach ($packages as $package) {
            if (!$package->isApplication()) {
                continue;
            }

            $activeApplication = new stdClass();
            $activeApplication->value = $package->getContext();
            $activeApplication->label = $this->translator->trans('TypeName', [], $package->getContext());
            $activeApplication->attributes = [];

            $activeApplications[] = $activeApplication;
        }

        return $activeApplications;
    }

    /**
     * @return string[]
     */
    public function getConfigurationPropertyNames(): array
    {
        return [
            self::CONFIGURATION_APPLICATION,
            self::CONFIGURATION_USE_TRANSLATION,
            self::CONFIGURATION_COMPONENT,
            self::CONFIGURATION_EXTRA_PARAMETERS
        ];
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('cube', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->translator->trans('ApplicationItem', [], Manager::CONTEXT);
    }

    public function isSelected(Item $item, User $user): bool
    {
        $currentContext = $this->request->query->get(ApplicationInterface::PARAM_CONTEXT);
        $currentAction = $this->request->query->get(ApplicationInterface::PARAM_ACTION);

        if ($currentContext != $item->getSetting(self::CONFIGURATION_APPLICATION)) {
            return false;
        }

        $component = $item->getSetting(self::CONFIGURATION_COMPONENT);

        if ($component && $currentAction != $component) {
            return false;
        }

        return true;
    }

    public function mapDataToForms(array $viewData, FormInterface $form): void
    {
        $application = new stdClass();
        $application->value = $viewData[self::CONFIGURATION_APPLICATION];
        $application->label = $application->value;
        $application->attributes = [];

        $data = [];

        $data[self::CONFIGURATION_APPLICATION] = $application;
        $data[self::CONFIGURATION_USE_TRANSLATION] = (bool) $viewData[self::CONFIGURATION_USE_TRANSLATION];
        $data[self::CONFIGURATION_COMPONENT] = $viewData[self::CONFIGURATION_COMPONENT];
        $data[self::CONFIGURATION_EXTRA_PARAMETERS] = $viewData[self::CONFIGURATION_EXTRA_PARAMETERS];

        $form->setData($data);
    }

    public function mapFormsToData(FormInterface $form, mixed &$viewData): void
    {
        $viewData[self::CONFIGURATION_APPLICATION] = $form[self::CONFIGURATION_APPLICATION]->getData();
        $viewData[self::CONFIGURATION_USE_TRANSLATION] = $form[self::CONFIGURATION_USE_TRANSLATION]->getData() ? 1 : 0;
        $viewData[self::CONFIGURATION_COMPONENT] = $form[self::CONFIGURATION_COMPONENT]->getData();
        $viewData[self::CONFIGURATION_EXTRA_PARAMETERS] = $form[self::CONFIGURATION_EXTRA_PARAMETERS]->getData();
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        if ($item->getSetting(self::CONFIGURATION_USE_TRANSLATION)) {
            return $this->translator->trans('TypeName', [], $item->getSetting(self::CONFIGURATION_APPLICATION));
        }

        return $this->determineItemTitleForCurrentLanguage($item);
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        if ($item->getSetting(self::CONFIGURATION_USE_TRANSLATION)) {
            return $this->translator->trans('TypeName', [], $item->getSetting(self::CONFIGURATION_APPLICATION), $isoCode
            );
        }

        return $this->determineItemTitleForIsoCode($item, $isoCode);
    }
}