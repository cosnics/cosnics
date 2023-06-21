<?php
namespace Chamilo\Core\Menu\Service\Renderer;

use Chamilo\Core\Menu\Architecture\Interfaces\TranslatableItemInterface;
use Chamilo\Core\Menu\Architecture\Traits\TranslatableItemTrait;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkApplicationItemRenderer extends ItemRenderer implements TranslatableItemInterface
{
    use TranslatableItemTrait;

    public const CONFIGURATION_SECTION = 'section';
    public const CONFIGURATION_TARGET = 'target';
    public const CONFIGURATION_URL = 'url';

    public const TARGET_BLANK = '_blank';
    public const TARGET_PARENT = '_parent';
    public const TARGET_SELF = '_self';
    public const TARGET_TOP = '_top';

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, array $fallbackIsoCodes
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->fallbackIsoCodes = $fallbackIsoCodes;
    }

    public function render(Item $item, User $user): string
    {
        $html = [];

        $html[] = '<li>';
        $html[] = '<a href="' . $item->getSetting(self::CONFIGURATION_URL) . '" target="' .
            $item->getSetting(self::CONFIGURATION_TARGET) . '">';
        $html[] = '<div>' . $this->renderTitle($item) . '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function renderTitle(Item $item): string
    {
        return $this->determineItemTitleForCurrentLanguage($item);
    }
}