<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RepositoryCategoryService
{
    /**
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param int $parentIdentifier
     * @param string $categoryName
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory|null
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function createNewCategoryInWorkspace(
        WorkspaceInterface $workspace, int $parentIdentifier, string $categoryName
    )
    {
        $existingCategory = $this->findCategoryFromParameters($workspace, $parentIdentifier, $categoryName);

        if ($existingCategory instanceof RepositoryCategory)
        {
            return $existingCategory;
        }
        else
        {
            $category = new RepositoryCategory();
            $category->set_name($categoryName);
            $category->set_parent($parentIdentifier);
            $category->set_type_id($workspace->getId());
            $category->set_type($workspace->getWorkspaceType());

            if (!$category->create())
            {
                return null;
            }
            else
            {
                return $category;
            }
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param int $parentIdentifier
     * @param string $categoryName
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function findCategoryFromParameters(
        WorkspaceInterface $workspace, int $parentIdentifier, string $categoryName
    )
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($parentIdentifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($workspace->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($workspace->getWorkspaceType())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_NAME),
            new StaticConditionVariable($categoryName)
        );
        $condition = new AndCondition($conditions);

        return DataManager::retrieve(
            RepositoryCategory::class, new DataClassRetrieveParameters($condition)
        );
    }
}