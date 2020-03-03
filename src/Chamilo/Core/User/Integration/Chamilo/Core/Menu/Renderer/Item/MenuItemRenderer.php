<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\ItemCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class MenuItemRenderer extends ItemRenderer
{

    /**
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemCacheService $itemCacheService
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemCacheService $itemCacheService,
        Theme $themeUtilities, ChamiloRequest $request, ClassnameUtilities $classnameUtilities
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $themeUtilities, $request);

        $this->classnameUtilities = $classnameUtilities;
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
        $html = array();

        $selected = $this->isSelected($item);

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a href="' . $this->getUrl() . '">';

        $title = $this->renderTitle($item);

        if ($item->showIcon())
        {
            $itemNamespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname($item->getType());
            $itemNamespace = ClassnameUtilities::getInstance()->getNamespaceParent($itemNamespace, 2);
            $itemType = ClassnameUtilities::getInstance()->getClassnameFromNamespace($item->getType());
            $imagePath = Theme::getInstance()->getImagePath($itemNamespace, $itemType . ($selected ? 'Selected' : ''));

            $html[] = '<img src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
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
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities): void
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * @return string
     */
    abstract public function getUrl();
}