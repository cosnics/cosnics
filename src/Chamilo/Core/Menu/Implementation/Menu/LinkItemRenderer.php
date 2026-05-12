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
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use stdClass;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Implementation\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class LinkItemRenderer extends ItemRenderer
    implements TranslatableItemInterface, ConfigurableItemInterface, SelectableItemInterface
{
    use TranslatableItemTrait;

    public const string CONFIGURATION_TARGET = 'target';
    public const string CONFIGURATION_URL = 'url';

    public function __construct(
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        protected ClassnameUtilities $classnameUtilities, protected WebPathBuilder $webPathBuilder,
        protected FormTypeBuilder $formTypeBuilder, protected array $fallbackIsoCodes
    )
    {
        parent::__construct($translator, $itemCacheService, $request);
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

    public function addConfigurationToForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilder = $this->formTypeBuilder;

        $builder->add(
            $formTypeBuilder->createCategory(
                $builder, 'category_properties', $this->translator->trans('Properties', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $formTypeBuilder->createText(
                $builder, self::CONFIGURATION_URL, $this->translator->trans('Url', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $formTypeBuilder->createSelect(
                $builder, self::CONFIGURATION_TARGET, $this->translator->trans('Target', [], Manager::CONTEXT), true,
                $this->getTargetOptions()
            )
        );
    }

    /**
     * @return string[]
     */
    public function getConfigurationPropertyNames(): array
    {
        return [self::CONFIGURATION_URL, self::CONFIGURATION_TARGET];
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('link', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->translator->trans('LinkItem', [], Manager::CONTEXT);
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

    public function isSelected(Item $item, User $user): bool
    {
        $urlParts = parse_url($item->getSetting(self::CONFIGURATION_URL));

        $basePath = $this->webPathBuilder->getBasePath();
        $urlBasePath = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];

        if ($basePath == $urlBasePath) {
            parse_str($urlParts['query'], $queryParts);

            foreach ($queryParts as $queryPartVariable => $queryPartValue) {
                if (!$this->request->query->has($queryPartVariable) ||
                    $this->request->query->get($queryPartVariable) !== $queryPartValue) {
                    return false;
                }
            }
        }
        else {
            return false;
        }

        return true;
    }

    public function mapDataToForms(array $viewData, FormInterface $form): void
    {
        $target = new stdClass();
        $target->value = $viewData[self::CONFIGURATION_TARGET];
        $target->label = $target->value;
        $target->attributes = [];

        $data = [];

        $data[self::CONFIGURATION_TARGET] = $target;
        $data[self::CONFIGURATION_URL] = $viewData[self::CONFIGURATION_URL];

        $form->setData($data);
    }

    public function mapFormsToData(FormInterface $form, mixed &$viewData): void
    {
        $viewData[self::CONFIGURATION_TARGET] = $form[self::CONFIGURATION_TARGET]->getData()->value;
        $viewData[self::CONFIGURATION_URL] = $form[self::CONFIGURATION_URL]->getData();
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