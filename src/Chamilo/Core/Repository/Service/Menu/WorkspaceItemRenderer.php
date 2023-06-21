<?php
namespace Chamilo\Core\Repository\Service\Menu;

use Chamilo\Core\Menu\Architecture\Interfaces\SelectableItemInterface;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceItemRenderer extends ItemRenderer implements SelectableItemInterface
{
    public const CONFIGURATION_NAME = 'name';
    public const CONFIGURATION_WORKSPACE_ID = 'workspace_id';

    protected UrlGenerator $urlGenerator;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->urlGenerator = $urlGenerator;
    }

    public function render(Item $item, User $user): string
    {
        $selected = $this->isSelected($item, $user);

        $workspaceUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_WORKSPACE_ID => $item->getSetting(self::CONFIGURATION_WORKSPACE_ID)
            ]
        );

        $html = [];

        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = '<a href="' . $workspaceUrl . '">';
        $title = $this->renderTitle($item);

        if ($item->showIcon())
        {
            $glyph = new FontAwesomeGlyph('hdd', [], null, 'fas');
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

    public function isSelected(Item $item, User $user): bool
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);
        $currentWorkspace = $request->query->get(Manager::PARAM_WORKSPACE_ID);

        return $currentContext == Manager::CONTEXT &&
            $currentWorkspace == $item->getSetting(self::CONFIGURATION_WORKSPACE_ID);
    }

    public function renderTitle(Item $item): string
    {
        return $item->getSetting(self::CONFIGURATION_NAME);
    }
}