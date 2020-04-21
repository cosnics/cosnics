<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Exception;
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
        try
        {
            return new JsonResponse($this->getCategories());
        }
        catch(Exception $ex)
        {
            return $this->handleException($ex);
        }
    }

    /**
     * get categories for the logged in user
     */
    protected function getCategories()
    {
        $workspace = $this->getWorkspaceFromRequest();

        $categorymenu = new ContentObjectCategoryMenu($workspace);
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');

        $categories = $renderer->toArray();
        $categoriesArray = array();

        foreach ($categories as $id => $name)
        {
            $categoriesArray[] = ["id" => $id, "name" => $name];
        }

        return $categoriesArray;
    }

}