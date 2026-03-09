<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\DataClassUrlGenerator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;

/**
 * @package Chamilo\Core\Group\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupUrlGenerator
{
    protected DataClassUrlGenerator $dataClassUrlGenerator;

    protected UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator, DataClassUrlGenerator $dataClassUrlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->dataClassUrlGenerator = $dataClassUrlGenerator;
    }

    public function getCreateUrl(Group $parentGroup): string
    {
        return $this->getGroupActionUrl(ActionEnum::CREATE->value, $parentGroup);
    }

    public function getDataClassUrlGenerator(): DataClassUrlGenerator
    {
        return $this->dataClassUrlGenerator;
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
        return $this->getDataClassUrlGenerator()->getActionUrl(
            Manager::CONTEXT, Application::PARAM_ACTION, Manager::PARAM_GROUP_ID, $action, $group, $additionalParameters
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
            ActionEnum::SUBSCRIBE->value, $group, [Manager::PARAM_USER_ID => $user->getId()]
        );
    }

    public function getTruncateUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::TRUNCATE->value, $group);
    }

    public function getUnsubscribeUserUrl(SubscribedUser $subscribedUser): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => ActionEnum::UNSUBSCRIBE->value,
                Manager::PARAM_RELATION_ID => $subscribedUser->getRelationId()
            ]
        );
    }

    public function getUpdateUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::UPDATE->value, $group);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getViewUrl(Group $group): string
    {
        return $this->getGroupActionUrl(ActionEnum::BROWSE->value, $group);
    }
}