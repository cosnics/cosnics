<?php
namespace Chamilo\Configuration\Category\Service;

use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * @package Chamilo\Configuration\Category\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CategoryManagerImplementerInterface
{
    public function areSubcategoriesAllowed(): bool;

    public function countCategories(?Condition $condition = null): int;

    public function countSubCategories(PlatformCategory $category): int;

    public function doCategoriesHaveImpact(array $categoryIdentifiers = []): bool;

    public function getBrowseCategoriesUrl(PlatformCategory $category): string;

    public function getCategoryClassName(): string;

    public function getChangeCategoryParentUrl(PlatformCategory $category): string;

    public function getDeleteCategoryUrl(PlatformCategory $category): string;

    public function getImpactViewUrl(PlatformCategory $category): string;

    public function getMoveCategoryUrl(PlatformCategory $category): string;

    public function getToggleVisibilityCategoryUrl(PlatformCategory $category): string;

    public function getUpdateCategoryUrl(PlatformCategory $category): string;

    public function isAllowedToAddToCategory(PlatformCategory $category): bool;

    public function isAllowedToChangeCategoryVisibility(PlatformCategory $category): bool;

    public function isAllowedToDeleteCategory(PlatformCategory $category): bool;

    public function isAllowedToEditCategory(PlatformCategory $category): bool;

    public function retrieveCategories(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    );

    public function supportsCategoryVisibility(): bool;

    public function supportsImpactView(): bool;
}