<?php
namespace Chamilo\Core\Repository\Service\Menu;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Workspace\Manager as WorkspaceManager;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceCategoryItemRenderer extends ItemRenderer
{

    protected WorkspaceService $workspaceService;

    private ItemRendererFactory $itemRendererFactory;

    public function __construct(
        WorkspaceService $workspaceService, AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, ItemRendererFactory $itemRendererFactory
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->itemRendererFactory = $itemRendererFactory;
        $this->workspaceService = $workspaceService;
    }

    /**
     * @throws \Exception
     */
    public function render(Item $item, User $user): string
    {
        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $html = [];

        $selected = $this->isSelected($item, $user);

        $html[] = '<li class="dropdown' . ($selected ? ' active' : '') . '">';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        $title = $this->getTranslator()->trans('Workspaces', [], 'Chamilo\Core\Repository');

        if ($item->showIcon())
        {
            $glyph = new FontAwesomeGlyph('server', [], null, 'fas');
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

        $html[] = $this->renderWorkspaces($item, $user);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findWorkspaces(User $user): ArrayCollection
    {
        return $this->getWorkspaceService()->findFavouriteWorkspacesByUser($user);
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->workspaceService;
    }

    public function isItemVisibleForUser(User $user): bool
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\Repository');
    }

    public function isSelected(Item $item, User $user): bool
    {
        $request = $this->getRequest();

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);

        if ($currentContext == WorkspaceManager::CONTEXT)
        {
            return true;
        }

        $userWorkspaces = $this->getWorkspaceService()->findFavouriteWorkspaceIdentifiersByUser($user);
        $currentWorkspace = $request->query->get(RepositoryManager::PARAM_WORKSPACE_ID);

        return in_array($currentWorkspace, $userWorkspaces);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function renderWorkspaces(Item $item, User $user): string
    {
        $itemRendererFactory = $this->getItemRendererFactory();
        $workspaces = $this->findWorkspaces($user);

        $html = [];

        $html[] = '<ul class="dropdown-menu">';

        if ($workspaces->count())
        {
            foreach ($workspaces as $workspace)
            {
                $workspaceItem = new Item();
                $workspaceItem->setType(WorkspaceItemRenderer::class);
                $workspaceItem->setSetting(WorkspaceItemRenderer::CONFIGURATION_WORKSPACE_ID, $workspace->getId());
                $workspaceItem->setSetting(WorkspaceItemRenderer::CONFIGURATION_NAME, $workspace->getName());
                $workspaceItem->setParentId($item->getId());
                $workspaceItem->setDisplay(Item::DISPLAY_TEXT);

                $itemRenderer = $itemRendererFactory->getItemRenderer($workspaceItem);

                $html[] = $itemRenderer->render($workspaceItem, $user);
            }

            $html[] = '<li role="separator" class="divider"></li>';
        }

        $configurationItem = new Item();
        $configurationItem->setType(WorkspaceConfigureItemRenderer::class);
        $configurationItem->setParentId($item->getId());
        $configurationItem->setDisplay(Item::DISPLAY_TEXT);

        $itemRenderer = $itemRendererFactory->getItemRenderer($configurationItem);

        $html[] = $itemRenderer->render($configurationItem, $user);

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}