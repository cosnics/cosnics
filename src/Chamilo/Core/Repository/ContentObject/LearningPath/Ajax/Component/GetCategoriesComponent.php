<?php


namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class GetCategoriesComponent
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class GetCategoriesComponent extends Manager
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $response = new JsonResponse($this->getCategories());
        $response->send();
    }

    /**
     * get categories for the logged in user
     */
    protected function getCategories()
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        $repository = $workspaceService->getPersonalWorkspaceForUser($this->getUser());

        $categorymenu = new ContentObjectCategoryMenu($repository);
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');

        $categories = $renderer->toArray();
        $categoriesArray = array();

        foreach ($categories as $id => $name) {
            $categoriesArray[] = ["id" => $id, "name" => $name];
        }

        return $categoriesArray;
    }

}