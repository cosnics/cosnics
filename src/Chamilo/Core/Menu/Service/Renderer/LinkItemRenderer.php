<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Core\Menu\Architecture\Interfaces\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Traits\TranslatableItemTrait;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkItemRenderer extends ItemRenderer implements TranslatableItemInterface
{
    use TranslatableItemTrait;

    public const CONFIGURATION_TARGET = 'target';
    public const CONFIGURATION_URL = 'url';

    public const TARGET_BLANK = '_blank';
    public const TARGET_PARENT = '_parent';
    public const TARGET_SELF = '_self';
    public const TARGET_TOP = '_top';

    private ClassnameUtilities $classnameUtilities;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, ClassnameUtilities $classnameUtilities,
        array $fallbackIsoCodes
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->classnameUtilities = $classnameUtilities;
        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    public function render(Item $item, User $user): string
    {
        $title = $this->renderTitle($item);

        $html = [];

        $html[] = '<li>';
        $html[] = '<a href="' . $item->getSetting(self::CONFIGURATION_URL) . '" target="' .
            $item->getSetting(self::CONFIGURATION_TARGET) . '">';

        if ($item->showIcon())
        {
            $iconClass = $item->getIconClass() ? $item->getIconClass() : 'link';

            $glyph = new FontAwesomeGlyph($iconClass, ['fa-2x'], null, 'fas');
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

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function renderTitle(Item $item): string
    {
        return $this->determineItemTitleForCurrentLanguage($item);
    }
}