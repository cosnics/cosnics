<?php
namespace Chamilo\Core\Group\Service\UserDetails;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Traits\UserDetailsRendererTrait;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Service\UserDetails
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRenderer implements UserDetailsRendererInterface
{
    use UserDetailsRendererTrait;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, GroupsTreeTraverser $groupsTreeTraverser
    )
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    public function getGlyph(): InlineGlyph
    {
        return new NamespaceIdentGlyph(Manager::CONTEXT, true);
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function hasContentForUser(User $user, User $requestingUser): bool
    {
        return $this->getGroupsTreeTraverser()->findAllSubscribedGroupsForUserIdentifier($user->getId())->count() > 0;
    }

    public function renderTitle(User $user, User $requestingUser): string
    {
        return $this->getTranslator()->trans('TypeName', [], Manager::CONTEXT);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function renderUserDetails(User $user, User $requestingUser): string
    {
        $translator = $this->getTranslator();

        $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        $table->setHeaderContents(0, 0, $translator->trans('Groups', [], Manager::CONTEXT));
        $table->setCellAttributes(0, 0, ['colspan' => 2, 'style' => 'text-align: center;']);

        $table->setHeaderContents(1, 0, $translator->trans('GroupCode', [], Manager::CONTEXT));
        $table->setCellAttributes(1, 0, ['style' => 'width: 150px;']);
        $table->setHeaderContents(1, 1, $translator->trans('GroupName', [], Manager::CONTEXT));

        $groups = $this->getGroupsTreeTraverser()->findAllSubscribedGroupsForUserIdentifier($user->getId());

        if ($groups->count() == 0)
        {
            $table->setCellContents(2, 0, $translator->trans('NoGroups', [], Manager::CONTEXT));
            $table->setCellAttributes(2, 0, ['colspan' => 2, 'style' => 'text-align: center;']);
        }
        else
        {
            $i = 2;

            foreach ($groups as $group)
            {
                $viewUrl = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_VIEW_GROUP,
                        Manager::PARAM_GROUP_ID => $group->getId()
                    ]
                );

                $url = '<a href="' . $viewUrl . '">';

                $table->setCellContents($i, 0, $url . $group->get_code() . '</a>');
                $table->setCellAttributes($i, 0, ['style' => 'width: 150px;']);
                $table->setCellContents($i, 1, $url . $group->get_name() . '</a>');
                $i ++;
            }
        }

        return $table->toHtml();
    }
}