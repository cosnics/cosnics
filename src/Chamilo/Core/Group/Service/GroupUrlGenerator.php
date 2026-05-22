<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Component\SubscribeComponent;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\DataClassUrlGenerator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupUrlGenerator
{
    public function __construct(
        protected UrlGenerator $urlGenerator, protected DataClassUrlGenerator $dataClassUrlGenerator
    )
    {
    }

    public function getCreateUrl(Group $parentGroup): string
    {
        return $this->getGroupActionUrl(ActionEnum::CREATE->value, $parentGroup);
    }

    public function getDeleteUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::DELETE->value, $group);
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getGroupActionUrl(string $action, Group $group, array $additionalParameters = []): string
    {
        return $this->dataClassUrlGenerator->getActionUrl(
            Manager::CONTEXT, ApplicationInterface::PARAM_ACTION, DataClass::PROPERTY_ID, $action, $group,
            $additionalParameters
        );
    }

    public function getMoveUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::MOVE->value, $group);
    }

    public function getSubscribeUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::BROWSE_NON_SUBSCRIBED_USERS->value, $group);
    }

    public function getSubscribeUserUrl(Group $group, User $user): string
    {
        return $this->getGroupActionUrl(
            ActionEnum::SUBSCRIBE->value, $group,
            [SubscribeComponent::PARAM_USER_ID => $user->getIdentifier()->toString()]
        );
    }

    public function getTruncateUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::TRUNCATE->value, $group);
    }

    public function getUnsubscribeUserUrl(GroupMembership $subscribedUser): string
    {
        return $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::UNSUBSCRIBE->value,
                DataClass::PROPERTY_ID => $subscribedUser->getIdentifier()->toString()
            ]
        );
    }

    public function getUpdateUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::UPDATE->value, $group);
    }

    public function getViewUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::BROWSE->value, $group);
    }
}