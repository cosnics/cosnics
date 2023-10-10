<?php
namespace Chamilo\Core\Group\Service;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\SubscribedUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\DataClassUrlGenerator;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;

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
        return $this->getGroupActionUrl(Manager::ACTION_CREATE_GROUP, $parentGroup);
    }

    public function getDataClassUrlGenerator(): DataClassUrlGenerator
    {
        return $this->dataClassUrlGenerator;
    }

    public function getDeleteUrl(Group $group): string
    {
        return $this->getGroupActionUrl(Manager::ACTION_DELETE_GROUP, $group);
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

    public function getMetadataUrl(Group $group): string
    {
        return $this->getGroupActionUrl(Manager::ACTION_MANAGE_METADATA, $group);
    }

    public function getMoveUrl(Group $group): string
    {
        return $this->getGroupActionUrl(Manager::ACTION_MOVE_GROUP, $group);
    }

    public function getSubscribeUrl(Group $group): string
    {
        return $this->getGroupActionUrl(Manager::ACTION_SUBSCRIBE_USER_BROWSER, $group);
    }

    public function getSubscribeUserUrl(Group $group, User $user): string
    {
        return $this->getGroupActionUrl(
            Manager::ACTION_SUBSCRIBE_USER_TO_GROUP, $group, [Manager::PARAM_USER_ID => $user->getId()]
        );
    }

    public function getTruncateUrl(Group $group): string
    {
        return $this->getGroupActionUrl(Manager::ACTION_TRUNCATE_GROUP, $group);
    }

    public function getUnsubscribeUserUrl(SubscribedUser $subscribedUser): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE_USER_FROM_GROUP,
                Manager::PARAM_GROUP_REL_USER_ID => $subscribedUser->getRelationId()
            ]
        );
    }

    public function getUpdateUrl(Group $group): string
    {
        return $this->getGroupActionUrl(Manager::ACTION_EDIT_GROUP, $group);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getViewUrl(Group $group): string
    {
        return $this->getGroupActionUrl(Manager::ACTION_VIEW_GROUP, $group);
    }
}