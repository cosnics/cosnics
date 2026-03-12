<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class GroupXmlFeedComponent extends Manager
{
    protected GroupsTreeTraverser $groupsTreeTraverser;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        GroupMembershipService $groupMembershipService, GroupUrlGenerator $groupUrlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupService $groupService,
        UserService $userService, UrlGenerator $urlGenerator, GroupsTreeTraverser $groupsTreeTraverser
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $groupMembershipService,
            $groupUrlGenerator, $alertsManager, $breadcrumbTrail, $groupService, $userService, $urlGenerator
        );

        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User) {
            throw new NotAllowedException();
        }

        $groupsTree = $this->getGroupService()->findGroupsForParentIdentifier(
            $this->getRequest()->query->get(NestedSet::PROPERTY_PARENT_ID)
        );

        $html = [];

        $html[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $html[] = '<tree>';
        $html[] = $this->renderGroupsTree($groupsTree);
        $html[] = '</tree>';

        return new Response(implode(PHP_EOL, $html), 200, ['Content-Type' => 'text/xml']);
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group> $groups
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function renderGroupsTree(ArrayCollection $groups): string
    {
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');
        $html[] = [];

        foreach ($groups as $group) {
            $description = strip_tags(
                $this->getGroupsTreeTraverser()->getFullyQualifiedNameForGroup($group) . ' [' . $group->getCode() . ']'
            );

            $hasChildren = $group->hasChildren() ? 1 : 0;
            $html[] =
                '<leaf id="' . $group->getId() . '" classes="' . $glyph->getClassNamesString() . '" has_children="' .
                $hasChildren . '" title="' . htmlspecialchars($group->getName()) . '" description="' .
                htmlspecialchars($description) . '"/>' . PHP_EOL;
        }

        return implode(PHP_EOL, $html);
    }
}