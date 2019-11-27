<?php

namespace Chamilo\Core\Repository\Storage\Repository;

use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class CategoryRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * CategoryRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param User $user
     *
     * @return RepositoryCategory[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function getAllCategoriesForUser(User $user)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable(PersonalWorkspace::WORKSPACE_TYPE)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($user->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieves(
            RepositoryCategory::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param RepositoryCategory $repositoryCategory
     *
     * @return int
     */
    public function getNextDisplayOrderForCategory(RepositoryCategory $repositoryCategory)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($repositoryCategory->get_parent())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($repositoryCategory->get_type_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($repositoryCategory->get_type())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieveNextValue(
            RepositoryCategory::class_name(),
            RepositoryCategory::PROPERTY_DISPLAY_ORDER,
            $condition
        );
    }

    /**
     * @param RepositoryCategory $category
     *
     * @return bool
     */
    public function createCategory(RepositoryCategory $category)
    {
        return $this->dataClassRepository->create($category);
    }

    /**
     * @param int $categoryId
     *
     * @return RepositoryCategory|DataClass
     */
    public function findCategoryById(int $categoryId)
    {
        return $this->dataClassRepository->retrieveById(RepositoryCategory::class, $categoryId);
    }
}
