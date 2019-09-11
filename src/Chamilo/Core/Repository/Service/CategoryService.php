<?php

namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\Repository\CategoryRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class CategoryService
 * @package Chamilo\Core\Repository\Service
 */
class CategoryService
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * CategoryService constructor.
     *
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param User $user
     *
     * @return RepositoryCategory[]
     */
    public function getAllCategoriesForUser(User $user)
    {
        return $this->categoryRepository->getAllCategoriesForuser($user);
    }

    /**
     * Returns the categories as a recursive tree. This method only returns the root categories with the
     * child categories already mapped in the root categories recursively.
     *
     * @param User $user
     *
     * @return RepositoryCategory[]
     */
    public function getAllCategoriesForUserAsTree(User $user)
    {
        $categories = $this->getAllCategoriesForUser($user);

        $categoriesByParent = $this->sortCategoriesByParentId($categories);

        foreach($categories as $category)
        {
            if(!array_key_exists($category->getId(), $categoriesByParent))
            {
                continue;
            }

            $category->setChildren($categoriesByParent[$category->getId()]);
        }

        return $categoriesByParent[0];
    }

    /**
     * Prepares the category tree for usage in a select element in a form
     *
     * @param User $user
     *
     * @return array
     */
    public function getCategoryTreeForForm(User $user)
    {
        $rootCategories = $this->getAllCategoriesForUserAsTree($user);
        $categoryList = [];

        $this->addCategoriesToList($rootCategories, $categoryList);

        return $categoryList;
    }

    /**
     * @param RepositoryCategory[] $categories
     * @param array $categoryList
     * @param int $level
     */
    protected function addCategoriesToList($categories, &$categoryList, int $level = 0)
    {
        foreach($categories as $category)
        {
            $categoryList[str_repeat('---', $level) . ' ' . $category->get_name()] = $category->getId();

            $level++;
            $this->addCategoriesToList($category->getChildren(), $categoryList, $level);
            $level--;
        }
    }

    /**
     * @param RepositoryCategory[] $categories
     *
     * @return RepositoryCategory[][]
     */
    protected function sortCategoriesByParentId($categories)
    {
        $sortedCategories = [];

        foreach($categories as $category)
        {
            $sortedCategories[$category->get_parent()][] = $category;
        }

        return $sortedCategories;
    }
}
