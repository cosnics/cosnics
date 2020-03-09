<?php
namespace Chamilo\Core\Menu\Renderer\Item;

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
 * @package Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkItemRenderer extends ItemRenderer
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
     * @param \Chamilo\Core\Menu\Storage\DataClass\LinkItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function render(Item $item, User $user)
    {
        $classnameUtilities = $this->getClassnameUtilities();

        $itemNamespace = $classnameUtilities->getNamespaceFromClassname($item->getType());
        $itemNamespace = $classnameUtilities->getNamespaceParent($itemNamespace, 2);
        $itemType = $classnameUtilities->getClassnameFromNamespace($item->getType());
        $imagePath = $this->getThemeUtilities()->getImagePath($itemNamespace, $itemType);

        $title = $this->renderTitle($item);

        $html = array();

        $html[] = '<li>';
        $html[] = '<a href="' . $item->getUrl() . '" target="' . $item->getTargetString() . '">';

        if ($item->showIcon())
        {
            if (!empty($item->getIconClass()))
            {
                $html[] = $this->renderCssIcon($item);
            }
            else
            {
                $html[] = '<img src="' . $imagePath . '" alt="' . $title . '" />';
            }
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
}