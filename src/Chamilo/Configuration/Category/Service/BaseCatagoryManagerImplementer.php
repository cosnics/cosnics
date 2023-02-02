<?php
namespace Chamilo\Configuration\Category\Service;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Configuration\Category\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class BaseCatagoryManagerImplementer implements CategoryManagerImplementerInterface
{
    protected UrlGenerator $urlGenerator;

    public function areSubcategoriesAllowed(): bool
    {
        // TODO: Implement areSubcategoriesAllowed() method.
    }

    public function countCategories(?Condition $condition = null): int
    {
        // TODO: Implement countCategories() method.
    }

    public function countSubCategories(PlatformCategory $category): int
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(get_class($category), PlatformCategory::PROPERTY_PARENT),
            new StaticConditionVariable($category->getId())
        );

        return $this->countCategories($condition);
    }

    public function getBrowseCategoriesUrl(PlatformCategory $category, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_CATEGORIES;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getChangeCategoryParentUrl(PlatformCategory $category, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_CHANGE_CATEGORY_PARENT;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getCreateCategoryUrl(PlatformCategory $category, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_CREATE_CATEGORY;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getDeleteCategoryUrl(PlatformCategory $category, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_DELETE_CATEGORY;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getImpactViewUrl(PlatformCategory $category, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_IMPACT_VIEW;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getMoveCategoryUrl(PlatformCategory $category, int $direction = 1, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_MOVE_CATEGORY;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();
        $parameters[Manager::PARAM_DIRECTION] = $direction;

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getToggleVisibilityCategoryUrl(PlatformCategory $category, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_TOGGLE_CATEGORY_VISIBILITY;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getUpdateCategoryUrl(PlatformCategory $category, array $parameters = []): string
    {
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_UPDATE_CATEGORY;
        $parameters[Manager::PARAM_CATEGORY_ID] = $category->getId();

        return $this->getUrlGenerator()->fromRequest($parameters);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function isAllowedToAddToCategory(PlatformCategory $category): bool
    {
        // TODO: Implement isAllowedToChangeCategoryVisibility() method.
    }

    public function isAllowedToChangeCategoryVisibility(PlatformCategory $category): bool
    {
        // TODO: Implement isAllowedToChangeCategoryVisibility() method.
    }

    public function isAllowedToDeleteCategory(PlatformCategory $category): bool
    {
        // TODO: Implement isAllowedToDeleteCategory() method.
    }

    public function isAllowedToEditCategory(PlatformCategory $category): bool
    {
        // TODO: Implement isAllowedToEditCategory() method.
    }
}