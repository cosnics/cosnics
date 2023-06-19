<?php
namespace Chamilo\Core\User\Service\Menu;

use Chamilo\Core\Menu\Architecture\Interfaces\SelectableItemInterface;
use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class MenuItemRenderer extends ItemRenderer
{

    protected UrlGenerator $urlGenerator;

    private ClassnameUtilities $classnameUtilities;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, ClassnameUtilities $classnameUtilities,
        UrlGenerator $urlGenerator
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->classnameUtilities = $classnameUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    public function render(Item $item, User $user): string
    {
        $html = [];

        $selected = $this instanceof SelectableItemInterface && $this->isSelected($item, $user);

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a href="' . $this->getUrl() . '">';

        $title = $this->renderTitle();

        if ($item->showIcon())
        {
            $html[] = $this->getGlyph()->render();
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

    abstract public function getGlyph(): InlineGlyph;

    abstract public function getUrl(): string;

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    abstract public function renderTitle(): string;
}