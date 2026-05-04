<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
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
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupMembershipService $groupMembershipService,
        GroupService $groupService, GroupUrlGenerator $groupUrlGenerator, UserService $userService,
        protected readonly GroupsTreeTraverser $groupsTreeTraverser
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator, $alertsManager,
            $breadcrumbTrail, $groupMembershipService, $groupService, $groupUrlGenerator, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User) {
            throw new NotAllowedException();
        }

        $groupsTree = $this->groupService->retrieveDescendantsByParentIdentifier(
            $this->getRequest()->query->get(Group::PROPERTY_PARENT_ID, DataClass::EMPTY_UUID)
        );

        $html = [];

        $html[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $html[] = '<tree>';
        $html[] = $this->renderGroupsTree($groupsTree);
        $html[] = '</tree>';

        return new Response(implode(PHP_EOL, $html), 200, ['Content-Type' => 'text/xml']);
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
                $this->groupsTreeTraverser->determineFullyQualifiedNameByGroup($group) . ' [' . $group->getCode() . ']'
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