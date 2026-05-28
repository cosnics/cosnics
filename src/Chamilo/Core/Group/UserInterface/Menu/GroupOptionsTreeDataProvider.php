<?php
namespace Chamilo\Core\Group\UserInterface\Menu;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeDataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

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
     */
    protected function getChildDataClasses(Uuid|string $parentIdentifier): ArrayCollection
    {
        return $this->groupService->retrieveDescendantsByParentIdentifier($parentIdentifier);
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\TreeNode[]
     */
    public function getData(string|Uuid|null $identifier, array $excludedIdentifiers = []): array
    {
        $getIdentifier = function (Group $group) {
            return $group->getIdentifier()->toString();
        };

        $getText = function (Group $group) {
            return $group->getName();
        };

        return [$this->__getData($getIdentifier, $getText, $identifier)];
    }

    /**
     * @throws \Chamilo\Core\Group\Architecture\Exception\NoSuchGroupException
     */
    protected function getDataClassByIdentifier(string|Uuid $identifier): Group
    {
        return $this->groupService->retrieveGroupByIdentifier($identifier);
    }

    protected function getRootDataClass(): Group
    {
        return $this->groupService->retrieveRootGroup();
    }
}
