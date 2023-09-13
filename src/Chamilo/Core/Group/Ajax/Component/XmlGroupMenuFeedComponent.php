<?php
namespace Chamilo\Core\Group\Ajax\Component;

use Chamilo\Core\Group\Ajax\Manager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Group\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class XmlGroupMenuFeedComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $groups_tree = $this->getGroupService()->findGroupsForParentIdentifier(
            $this->getRequest()->query->get(NestedSet::PROPERTY_PARENT_ID)
        );

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL, '<tree>' . PHP_EOL;
        $this->dump_groups_tree($groups_tree);
        echo '</tree>';
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group> $groups
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function dump_groups_tree(ArrayCollection $groups): void
    {
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        foreach ($groups as $group)
        {
            $description = strip_tags(
                $this->getGroupsTreeTraverser()->getFullyQualifiedNameForGroup($group) . ' [' . $group->get_code() . ']'
            );

            $has_children = $group->hasChildren() ? 1 : 0;
            echo '<leaf id="' . $group->getId() . '" classes="' . $glyph->getClassNamesString() . '" has_children="' .
                $has_children . '" title="' . htmlspecialchars($group->get_name()) . '" description="' .
                htmlspecialchars($description) . '"/>' . PHP_EOL;
        }
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }
}