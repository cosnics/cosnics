<?php
namespace Chamilo\Core\Repository\Service\Menu;

use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceItemRenderer extends ItemRenderer
{
    protected UrlGenerator $urlGenerator;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user): string
    {
        $selected = $this->isSelected($item);

        $workspaceUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_WORKSPACE_ID => $item->getWorkspaceId()
            ]
        );

        $html = [];

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a href="' . $workspaceUrl . '">';
        $title = $this->renderTitle($item);

        if ($item->showIcon())
        {
            $glyph = $item->getGlyph();
            $glyph->setExtraClasses(['fa-2x']);
            $glyph->setTitle($title);

            $html[] = $glyph->render();
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

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem $item
     *
     * @return bool
     */
    public function isSelected(Item $item): bool
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);
        $currentWorkspace = $request->query->get(Manager::PARAM_WORKSPACE_ID);

        return $currentContext == Manager::CONTEXT && $currentWorkspace == $item->getWorkspaceId();
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem $item
     *
     * @return string
     */
    public function renderTitle(Item $item): string
    {
        return $item->getName();
    }
}