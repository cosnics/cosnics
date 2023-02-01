<?php
namespace Chamilo\Configuration\Category\Service;

use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Configuration\Category\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CategoryDataProviderInterface
{
    public function areSubcategoriesAllowed(): bool;

    public function countCategories(?Condition $condition = null): int;

    public function getBrowseCategoriesUrl(PlatformCategory $category): string;

    public function getCategoryClassName(): string;

    public function getChangeCategoryParentUrl(PlatformCategory $category): string;

    public function getDeleteCategoryUrl(PlatformCategory $category): string;

    public function getImpactViewUrl(PlatformCategory $category): string;

    public function getMoveCategoryUrl(PlatformCategory $category): string;

    public function getToggleVisibilityCategoryUrl(PlatformCategory $category): string;

    public function getUpdateCategoryUrl(PlatformCategory $category): string;

    public function isAllowedToChangeCategoryVisibility(PlatformCategory $category): bool;

    public function isAllowedToDeleteCategory(PlatformCategory $category): bool;

    public function isAllowedToEditCategory(PlatformCategory $category): bool;

    public function supportsImpactView(PlatformCategory $category): bool;
}