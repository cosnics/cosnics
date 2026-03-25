<?php
namespace Chamilo\Core\Menu\Implementation\Menu;

use Chamilo\Core\Menu\Architecture\Interface\ConfigurableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\SelectableItemInterface;
use Chamilo\Core\Menu\Architecture\Interface\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Trait\TranslatableItemTrait;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use stdClass;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Implementation\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkItemRenderer extends ItemRenderer
    implements TranslatableItemInterface, ConfigurableItemInterface, SelectableItemInterface
{
    use TranslatableItemTrait;

    public const string CONFIGURATION_TARGET = 'target';
    public const string CONFIGURATION_URL = 'url';
    public const string TARGET_BLANK = '_blank';
    public const string TARGET_PARENT = '_parent';
    public const string TARGET_SELF = '_self';
    public const string TARGET_TOP = '_top';

    protected FormTypeBuilder $formTypeBuilder;

    protected WebPathBuilder $webPathBuilder;

    private ClassnameUtilities $classnameUtilities;

    public function __construct(
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        ClassnameUtilities $classnameUtilities, WebPathBuilder $webPathBuilder, FormTypeBuilder $formTypeBuilder,
        array $fallbackIsoCodes
    )
    {
        parent::__construct($translator, $itemCacheService, $request);

        $this->classnameUtilities = $classnameUtilities;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
        $this->webPathBuilder = $webPathBuilder;
        $this->formTypeBuilder = $formTypeBuilder;
    }

    public function render(Item $item, User $user): string
    {
        $title = $this->renderTitleForCurrentLanguage($item);

        $html = [];

        $html[] = '<li class="nav-item">';
        $html[] = '<a class="text-center nav-link' . ($this->isSelected($item, $user) ? 'active' : '') . '" href="' .
            $item->getSetting(self::CONFIGURATION_URL) . '" target="' . $item->getSetting(self::CONFIGURATION_TARGET) .
            '">';

        if ($item->showIcon()) {
            if (!$item->getIconClass()) {
                $glyph = $this->getRendererTypeGlyph();
            }
            else {
                $glyph = new FontAwesomeGlyph($item->getIconClass(), ['fa-lg']);
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

    /**
     * @throws \QuickformException
     */
    public function addConfigurationToForm(FormBuilderInterface $builder, array $options): void
    {
        $translator = $this->getTranslator();
        $formTypeBuilder = $this->formTypeBuilder;

        $formTypeBuilder->addCategory(
            $builder, 'category_properties', $translator->trans('Properties', [], Manager::CONTEXT)
        );

        $formTypeBuilder->addText(
            $builder, self::CONFIGURATION_URL, $translator->trans('Url', [], Manager::CONTEXT), true
        );

        $formTypeBuilder->addSelect(
            $builder, self::CONFIGURATION_TARGET, $translator->trans('Target', [], Manager::CONTEXT), true,
            $this->getTargetOptions()
        );
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    /**
     * @return string[]
     */
    public function getConfigurationPropertyNames(): array
    {
        return [self::CONFIGURATION_URL, self::CONFIGURATION_TARGET];
    }

    public function getDefaultFormConfigurationData(Item $item): array
    {
        $configurationData = [];
        $configuration = $item->getConfiguration();

        $configurationData[self::CONFIGURATION_TARGET] = new stdClass();
        $configurationData[self::CONFIGURATION_TARGET]->value = $configuration[self::CONFIGURATION_TARGET];
        $configurationData[self::CONFIGURATION_TARGET]->label = '';
        $configurationData[self::CONFIGURATION_TARGET]->attributes = [];

        $configurationData[self::CONFIGURATION_URL] = $configuration[self::CONFIGURATION_URL];

        return $configurationData;
    }

    public function getFormTypeBuilder(): FormTypeBuilder
    {
        return $this->formTypeBuilder;
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('link', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('LinkItem', [], Manager::CONTEXT);
    }

    protected function getTargetOptions(): array
    {
        $targets = ['_blank', '_self', '_parent', '_top'];

        $options = [];

        foreach ($targets as $target) {
            $option = new stdClass();
            $option->value = $target;
            $option->label = $target;
            $option->attributes = [];

            $options[] = $option;
        }

        return $options;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    public function handleConfigurationData(mixed $submittedData): mixed
    {
        $processedData = [];

        $processedData[self::CONFIGURATION_TARGET] = $submittedData[self::CONFIGURATION_TARGET]->value;
        $processedData[self::CONFIGURATION_URL] = $submittedData[self::CONFIGURATION_URL];

        return $processedData;
    }

    public function isSelected(Item $item, User $user): bool
    {
        $urlParts = parse_url($item->getSetting(self::CONFIGURATION_URL));

        $basePath = $this->getWebPathBuilder()->getBasePath();
        $urlBasePath = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];

        if ($basePath == $urlBasePath) {
            parse_str($urlParts['query'], $queryParts);

            foreach ($queryParts as $queryPartVariable => $queryPartValue) {
                if (!$this->getRequest()->query->has($queryPartVariable) ||
                    $this->getRequest()->query->get($queryPartVariable) !== $queryPartValue) {
                    return false;
                }
            }
        }
        else {
            return false;
        }

        return true;
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->determineItemTitleForCurrentLanguage($item);
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->determineItemTitleForIsoCode($item, $isoCode);
    }
}