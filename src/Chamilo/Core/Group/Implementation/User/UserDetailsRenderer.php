<?php
namespace Chamilo\Core\Group\Implementation\User;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Architecture\Trait\UserDetailsRendererTrait;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\NamespaceIdentGlyph;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Implementation\User
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRenderer implements UserDetailsRendererInterface
{
    use UserDetailsRendererTrait;

    public function __construct(
        protected Translator $translator, protected UrlGenerator $urlGenerator,
        protected GroupMembershipService $groupMembershipService
    )
    {
    }

    public function getGlyph(): InlineGlyph
    {
        return new NamespaceIdentGlyph(Manager::CONTEXT, true);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function hasContentForUser(User $user, User $requestingUser): bool
    {
        return $this->groupMembershipService->retrieveGroupMembershipsByUserIdentifier($user->getIdentifier())->count(
            ) > 0;
    }

    public function renderTitle(User $user, User $requestingUser): string
    {
        return $this->translator->trans('TypeName', [], Manager::CONTEXT);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderUserDetails(User $user, User $requestingUser): string
    {
        $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        $table->setHeaderContents(0, 0, $this->translator->trans('Groups', [], Manager::CONTEXT));
        $table->setCellAttributes(0, 0, ['colspan' => 2, 'style' => 'text-align: center;']);

        $table->setHeaderContents(1, 0, $this->translator->trans('GroupCode', [], Manager::CONTEXT));
        $table->setCellAttributes(1, 0, ['style' => 'width: 150px;']);
        $table->setHeaderContents(1, 1, $this->translator->trans('GroupName', [], Manager::CONTEXT));

        $groupMemberships =
            $this->groupMembershipService->retrieveGroupMembershipsByUserIdentifier($user->getIdentifier());

        if ($groupMemberships->count() == 0) {
            $table->setCellContents(2, 0, $this->translator->trans('NoGroups', [], Manager::CONTEXT));
            $table->setCellAttributes(2, 0, ['colspan' => 2, 'style' => 'text-align: center;']);
        }
        else {
            $rowIndex = 2;

            foreach ($groupMemberships as $groupMembership) {
                $viewUrl = $this->urlGenerator->fromParameters(
                    [
                        ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                        DataClass::PROPERTY_ID => $groupMembership->getGroup()->getIdentifier()->toString()
                    ]
                );

                $url = '<a href="' . $viewUrl . '">';

                $table->setCellContents($rowIndex, 0, $url . $groupMembership->getGroup()->getCode() . '</a>');
                $table->setCellAttributes($rowIndex, 0, ['style' => 'width: 150px;']);
                $table->setCellContents($rowIndex, 1, $url . $groupMembership->getGroup()->getName() . '</a>');
                $rowIndex ++;
            }
        }

        return $table->toHtml();
    }
}