<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeDataProvider;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\UserInterface\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class GroupOptionsTreeDataProvider extends OptionsTreeDataProvider
{
    public function __construct(protected GroupService $groupService, protected GroupsTreeTraverser $groupsTreeTraverser
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getChildDataClasses(string $parentIdentifier): ArrayCollection
    {
        return $this->groupService->retrieveDescendantsByParentIdentifier($parentIdentifier);
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    public function getData(?string $identifier, array $excludedIdentifiers = []): array
    {
        $getIdentifier = function (Group $group) {
            return $group->getId();
        };

        $getText = function (Group $group) {
            return $group->getName();
        };

        return [$this->__getData($getIdentifier, $getText, $identifier)];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    protected function getDataClassByIdentifier(string $identifier): Group
    {
        return $this->groupService->retrieveGroupByIdentifier($identifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getRootDataClass(): Group
    {
        return $this->groupService->retrieveRootGroup();
    }
}
