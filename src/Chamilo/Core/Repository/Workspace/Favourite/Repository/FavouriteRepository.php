<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Repository;

use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Workspace\Favourite\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function createWorkspaceUserFavourite(WorkspaceUserFavourite $workspaceUserFavourite): bool
    {
        return $this->getDataClassRepository()->create($workspaceUserFavourite);
    }

    public function deleteWorkspaceUserFavourite(WorkspaceUserFavourite $workspaceUserFavourite): bool
    {
        return $this->getDataClassRepository()->delete($workspaceUserFavourite);
    }

    public function findFavouriteByIdentifier(string $identifier): ?WorkspaceUserFavourite
    {
        return $this->getDataClassRepository()->retrieveById(WorkspaceUserFavourite::class, $identifier);
    }

    public function findWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(User $user, string $workspaceIdentifier
    ): ?WorkspaceUserFavourite
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_USER_ID
            ), new StaticConditionVariable($user->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspaceIdentifier)
        );

        $condition = new AndCondition($conditions);

        return $this->getDataClassRepository()->retrieve(
            WorkspaceUserFavourite::class, new DataClassRetrieveParameters($condition)
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }
}