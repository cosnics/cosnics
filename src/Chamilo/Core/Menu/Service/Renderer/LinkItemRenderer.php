<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Core\Menu\Architecture\Interfaces\ConfigurableItemInterface;
use Chamilo\Core\Menu\Architecture\Interfaces\SelectableItemInterface;
use Chamilo\Core\Menu\Architecture\Interfaces\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Traits\TranslatableItemTrait;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkItemRenderer extends ItemRenderer
    implements TranslatableItemInterface, ConfigurableItemInterface, SelectableItemInterface
{
    use TranslatableItemTrait;

    public const CONFIGURATION_TARGET = 'target';
    public const CONFIGURATION_URL = 'url';

    public const TARGET_BLANK = '_blank';
    public const TARGET_PARENT = '_parent';
    public const TARGET_SELF = '_self';
    public const TARGET_TOP = '_top';

    protected WebPathBuilder $webPathBuilder;

    private ClassnameUtilities $classnameUtilities;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, ClassnameUtilities $classnameUtilities,
        WebPathBuilder $webPathBuilder, array $fallbackIsoCodes
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->classnameUtilities = $classnameUtilities;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
        $this->webPathBuilder = $webPathBuilder;
    }

    public function render(Item $item, User $user): string
    {
        $title = $this->renderTitleForCurrentLanguage($item);

        $html = [];

        $html[] = '<li class="' . ($this->isSelected($item, $user) ? 'active' : '') . '">';
        $html[] = '<a href="' . $item->getSetting(self::CONFIGURATION_URL) . '" target="' .
            $item->getSetting(self::CONFIGURATION_TARGET) . '">';

        if ($item->showIcon())
        {
            if (!$item->getIconClass())
            {
                $glyph = $this->getRendererTypeGlyph();
            }
            else
            {
                $glyph = new FontAwesomeGlyph($item->getIconClass(), ['fa-2x']);
            }

            $glyph->setExtraClasses(['fa-2x']);

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
     */
    public function addConfigurationToForm(FormValidator $formValidator): void
    {
        $formValidator->addElement('category', $this->getTranslator()->trans('Properties', [], Manager::CONTEXT));

        $formValidator->add_textfield(
            Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_URL . ']',
            $this->getTranslator()->trans('URL', [], Manager::CONTEXT), true, ['size' => '100']
        );

        $formValidator->addElement(
            'select', Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_TARGET . ']',
            $this->getTranslator()->trans('Target', [], Manager::CONTEXT), ['_blank', '_self', '_parent', '_top'],
            ['class' => 'form-control']
        );

        $formValidator->addRule(
            Item::PROPERTY_CONFIGURATION . '[' . self::CONFIGURATION_TARGET . ']',
            $this->getTranslator()->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
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

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('link', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('LinkItem', [], Manager::CONTEXT);
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    public function isSelected(Item $item, User $user): bool
    {
        $urlParts = parse_url($item->getSetting(self::CONFIGURATION_URL));

        $basePath = $this->getWebPathBuilder()->getBasePath();
        $urlBasePath = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];

        if ($basePath == $urlBasePath)
        {
            parse_str($urlParts['query'], $queryParts);

            foreach ($queryParts as $queryPartVariable => $queryPartValue)
            {
                if (!$this->getRequest()->query->has($queryPartVariable) ||
                    $this->getRequest()->query->get($queryPartVariable) !== $queryPartValue)
                {
                    return false;
                }
            }
        }
        else
        {
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