<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\ItemRenderer
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

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function render(Item $item, User $user)
    {
        $html = [];

        $selected = $this->isSelected($item);

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a href="' . $this->getUrl() . '">';

        $title = $this->renderTitle($item);

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

    /**
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    abstract public function getGlyph();

    /**
     * @return string
     */
    abstract public function getUrl();

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities): void
    {
        $this->classnameUtilities = $classnameUtilities;
    }
}