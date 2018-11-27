<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\NavigationBarRenderer;

use Chamilo\Core\Menu\Renderer\ItemRendererFactory;
use Chamilo\Core\Menu\Renderer\NavigationBarRenderer;
use Chamilo\Core\Menu\Renderer\NavigationBarRenderer\NavigationBarItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceCategoryItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceConfigureItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem;
use Chamilo\Core\Repository\Workspace\Manager as WorkspaceManager;
use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceCategoryItemRenderer extends NavigationBarItemRenderer
{

    /**
     * @var \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, ChamiloRequest $request, Translator $translator,
        ItemRendererFactory $itemRendererFactory, Theme $themeUtilities
    )
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @return \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     * @todo This shouldn't really be here like this
     */
    protected function findWorkspaces(User $user)
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        $entityService = new EntityService();

        return $workspaceService->getWorkspaceFavouritesByUser($entityService, $user);
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

        $title = $this->getTranslator()->trans('Workspaces', [], 'Chamilo\Core\Repository');

        if ($item->showIcon())
        {
            $integrationNamespace = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';
            $imagePath = $this->getThemeUtilities()->getImagePath(
                $integrationNamespace, 'WorkspaceCategory' . ($selected ? 'Selected' : '')
            );

            $html[] = '<img class="chamilo-menu-item-icon' .
                ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $imagePath . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';

        $html[] = $this->renderWorkspaces($item, $user);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
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

                $itemRenderer = $itemRendererFactory->getItemRenderer(NavigationBarRenderer::class, $workspaceItem);

                $html[] = $itemRenderer->render($workspaceItem, $user);
            }
        }

        $configurationItem = new WorkspaceConfigureItem();
        $configurationItem->setParentId($item->getId());
        $configurationItem->setDisplay($item->getDisplay());

        $itemRenderer = $itemRendererFactory->getItemRenderer(NavigationBarRenderer::class, $configurationItem);

        $html[] = $itemRenderer->render($configurationItem, $user);

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
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
}