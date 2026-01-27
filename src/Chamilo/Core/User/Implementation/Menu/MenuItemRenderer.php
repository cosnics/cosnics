<?php
namespace Chamilo\Core\User\Implementation\Menu;

use Chamilo\Core\Menu\Architecture\Interface\SelectableItemInterface;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
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
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        ClassnameUtilities $classnameUtilities, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($translator, $itemCacheService, $request);

        $this->classnameUtilities = $classnameUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    public function render(Item $item, User $user): string
    {
        $html = [];

        $selected = $this instanceof SelectableItemInterface && $this->isSelected($item, $user);

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a href="' . $this->getUrl() . '">';

        $title = $this->renderTitleForCurrentLanguage($item);

        if ($item->showIcon())
        {
            $glyph = $this->getRendererTypeGlyph();
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

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    abstract public function getUrl(): string;

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    abstract public function renderTitleForCurrentLanguage(Item $item): string;
}