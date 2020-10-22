<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\ItemCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceCategoryItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceConfigureItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem;
use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Workspace\Manager as WorkspaceManager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceCategoryItemRenderer extends ItemRenderer
{

    /**
     * @var \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemCacheService $itemCacheService
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemCacheService $itemCacheService,
        ThemePathBuilder $themePathBuilder, ChamiloRequest $request, ItemRendererFactory $itemRendererFactory
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $themePathBuilder, $request);

        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    public function render(Item $item, User $user)
    {
        if (!$this->isItemVisibleForUser($item, $user))
        {
            return;
        }

        $html = array();

        $selected = $this->isSelected($item);

        $html[] = '<li class="dropdown' . ($selected ? ' active' : '') . '">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        $title = $this->renderTitle($item);

        if ($item->showIcon())
        {
            $glyph = $item->getGlyph();
            $glyph->setExtraClasses(array('fa-2x'));
            $glyph->setTitle($title);

            $html[] = $glyph->render();
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';

        $html[] = $this->renderWorkspaces($item, $user);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     * @todo This shouldn't really be here like this
     */
    protected function findWorkspaces(User $user)
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        $entityService = new EntityService();

        return $workspaceService->getWorkspaceFavouritesByUserFast($user);
    }

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceCategoryItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isItemVisibleForUser(WorkspaceCategoryItem $item, User $user)
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\Repository');
    }

    public function isSelected(Item $item)
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);

        if ($currentContext == WorkspaceManager::package())
        {
            return true;
        }

        $currentWorkspace = $request->query->get(RepositoryManager::PARAM_WORKSPACE_ID);

        return isset($currentWorkspace);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return $this->getTranslator()->trans('Workspaces', [], 'Chamilo\Core\Repository');
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     * @throws \Exception
     */
    protected function renderWorkspaces(Item $item, User $user)
    {
        $itemRendererFactory = $this->getItemRendererFactory();
        $workspaces = $this->findWorkspaces($user);

        $html = array();

        $html[] = '<ul class="dropdown-menu">';

        if ($workspaces->size())
        {
            while ($workspace = $workspaces->next_result())
            {
                $workspaceItem = new WorkspaceItem();
                $workspaceItem->setWorkspaceId($workspace->getId());
                $workspaceItem->setName($workspace->getName());
                $workspaceItem->setParentId($item->getId());
                $workspaceItem->setDisplay($item->getDisplay());

                $itemRenderer = $itemRendererFactory->getItemRenderer($workspaceItem);

                $html[] = $itemRenderer->render($workspaceItem, $user);
            }
        }

        $configurationItem = new WorkspaceConfigureItem();
        $configurationItem->setParentId($item->getId());
        $configurationItem->setDisplay($item->getDisplay());

        $itemRenderer = $itemRendererFactory->getItemRenderer($configurationItem);

        $html[] = $itemRenderer->render($configurationItem, $user);

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}