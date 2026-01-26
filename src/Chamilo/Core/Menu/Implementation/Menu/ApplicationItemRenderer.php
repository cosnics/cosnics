<?php
namespace Chamilo\Core\Menu\Implementation\Menu;

use Chamilo\Configuration\Service\PackageBundlesCacheService;
use Chamilo\Core\Menu\Architecture\Interface\ConfigurableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\SelectableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Trait\TranslatableItemTrait;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer\ItemRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ApplicationItemRenderer extends ItemRenderer
    implements SelectableItemInterface, TranslatableItemInterface, ConfigurableItemInterface
{
    use TranslatableItemTrait;

    public const CONFIGURATION_APPLICATION = 'application';
    public const CONFIGURATION_COMPONENT = 'component';
    public const CONFIGURATION_EXTRA_PARAMETERS = 'extra_parameters';
    public const CONFIGURATION_USE_TRANSLATION = 'use_translation';

    private PackageBundlesCacheService $packageBundlesCacheService;

    private UrlGenerator $urlGenerator;

    public function __construct(
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        PackageBundlesCacheService $packageBundlesCacheService, UrlGenerator $urlGenerator, array $fallbackIsoCodes
    )
    {
        parent::__construct($translator, $itemCacheService, $request);

        $this->packageBundlesCacheService = $packageBundlesCacheService;
        $this->urlGenerator = $urlGenerator;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    public function render(Item $item, User $user): string
    {
        $html = [];

        $html[] = '<li class="' . ($this->isSelected($item, $user) ? 'active' : '') . '">';

        $title = $this->renderTitleForCurrentLanguage($item);

        $html[] = '<a href="' . $this->getApplicationItemUrl($item) . '">';

        if ($item->showIcon())
        {
            if (!empty($item->getIconClass()))
            {
                $glyph = new FontAwesomeGlyph($item->getIconClass(), ['fa-2x'], $title, 'fas');
            }
            else
            {
                $glyph = new NamespaceIdentGlyph(
                    $item->getSetting(self::CONFIGURATION_APPLICATION), false, false, false, IdentGlyph::SIZE_MEDIUM,
                    [], $title
                );
            }

            $html[] = $glyph->render();
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '</div>';
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function addConfigurationToForm(FormValidator $formValidator): void
    {
        $translator = $this->getTranslator();

        $formValidator->addElement('category', $translator->trans('Properties', [], 'Chamilo\Core\Menu'));

        $formValidator->addElement(
            'select', Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_APPLICATION . ']',
            $translator->trans('Application', [], 'Chamilo\Core\Menu'), $this->getApplicationOptions(),
            ['class' => 'form-control']
        );

        $formValidator->addRule(
            Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_APPLICATION . ']',
            $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );

        $formValidator->addElement(
            'checkbox', Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_USE_TRANSLATION . ']',
            $translator->trans('UseTranslation', [], 'Chamilo\Core\Menu')
        );

        $formValidator->addTextfield(
            Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_COMPONENT . ']',
            $translator->trans('Component', [], 'Chamilo\Core\Menu'), false
        );
        $formValidator->addTextfield(
            Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_EXTRA_PARAMETERS . ']',
            $translator->trans('ExtraParameters', [], 'Chamilo\Core\Menu'), false
        );
    }

    protected function getApplicationItemUrl(Item $item): string
    {
        $application = $item->getSetting(self::CONFIGURATION_APPLICATION);

        if ($application == 'root')
        {
            return $this->getUrlGenerator()->fromParameters();
        }

        $parameters = [];

        $parameters[Application::PARAM_CONTEXT] = $application;

        $component = $item->getSetting(self::CONFIGURATION_COMPONENT);

        if ($component)
        {
            $parameters[Application::PARAM_ACTION] = $component;
        }

        $extraParameters = $item->getSetting(self::CONFIGURATION_EXTRA_PARAMETERS);

        if ($extraParameters)
        {
            parse_str($extraParameters, $parsedExtraParameters);

            foreach ($parsedExtraParameters as $key => $value)
            {
                $parameters[$key] = $value;
            }
        }

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function getApplicationOptions(): array
    {
        $packages = $this->getPackageBundlesCacheService()->getPackages();

        $activeApplications = [];

        foreach ($packages as $package)
        {
            if (!$package->isApplication())
            {
                continue;
            }

            $activeApplications[$package->get_context()] =
                $this->getTranslator()->trans('TypeName', [], $package->get_context());
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

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->packageBundlesCacheService;
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('cube', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('ApplicationItem', [], Manager::CONTEXT);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function isSelected(Item $item, User $user): bool
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);
        $currentAction = $request->query->get(Application::PARAM_ACTION);

        if ($currentContext != $item->getSetting(self::CONFIGURATION_APPLICATION))
        {
            return false;
        }

        $component = $item->getSetting(self::CONFIGURATION_COMPONENT);

        if ($component && $currentAction != $component)
        {
            return false;
        }

        return true;
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        if ($item->getSetting(self::CONFIGURATION_USE_TRANSLATION))
        {
            return $this->getTranslator()->trans('TypeName', [], $item->getSetting(self::CONFIGURATION_APPLICATION));
        }

        return $this->determineItemTitleForCurrentLanguage($item);
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        if ($item->getSetting(self::CONFIGURATION_USE_TRANSLATION))
        {
            return $this->getTranslator()->trans('TypeName', [], $item->getSetting(self::CONFIGURATION_APPLICATION),
                $isoCode);
        }

        return $this->determineItemTitleForIsoCode($item, $isoCode);
    }
}