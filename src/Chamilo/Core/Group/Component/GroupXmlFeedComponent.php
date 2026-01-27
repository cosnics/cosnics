<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class GroupXmlFeedComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(): Response
    {
        $groups_tree = $this->getGroupService()->findGroupsForParentIdentifier(
            $this->getRequest()->query->get(NestedSet::PROPERTY_PARENT_ID)
        );

        $html = [];

        $html[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $html[] = '<tree>';
        $html[] = $this->dump_groups_tree($groups_tree);
        $html[] = '</tree>';

        return new Response(implode(PHP_EOL, $html), 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group> $groups
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function dump_groups_tree(ArrayCollection $groups): string
    {
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');
        $html[] = [];

        foreach ($groups as $group)
        {
            $description = strip_tags(
                $this->getGroupsTreeTraverser()->getFullyQualifiedNameForGroup($group) . ' [' . $group->getCode() . ']'
            );

            $has_children = $group->hasChildren() ? 1 : 0;
            $html[] =
                '<leaf id="' . $group->getId() . '" classes="' . $glyph->getClassNamesString() . '" has_children="' .
                $has_children . '" title="' . htmlspecialchars($group->getName()) . '" description="' .
                htmlspecialchars($description) . '"/>' . PHP_EOL;
        }

        return implode(PHP_EOL, $html);
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->getService(GroupsTreeTraverser::class);
    }
}