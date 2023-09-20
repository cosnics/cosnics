<?php
namespace Chamilo\Core\Group;

use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbGeneratorInterface;

/**
 * @package Chamilo\Core\Group
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE_GROUPS = 'Browser';
    public const ACTION_CREATE_GROUP = 'Creator';
    public const ACTION_DELETE_GROUP = 'Deleter';
    public const ACTION_EDIT_GROUP = 'Editor';
    public const ACTION_EXPORT = 'Exporter';
    public const ACTION_IMPORT = 'Importer';
    public const ACTION_IMPORT_GROUP_USERS = 'GroupUserImporter';
    public const ACTION_MANAGE_METADATA = 'MetadataManager';
    public const ACTION_MOVE_GROUP = 'Mover';
    public const ACTION_SUBSCRIBE_USER_BROWSER = 'SubscribeUserBrowser';
    public const ACTION_SUBSCRIBE_USER_TO_GROUP = 'Subscriber';
    public const ACTION_TRUNCATE_GROUP = 'Truncater';
    public const ACTION_UNSUBSCRIBE_USER_FROM_GROUP = 'Unsubscriber';
    public const ACTION_VIEW_GROUP = 'Viewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE_GROUPS;

    public const PARAM_COMPONENT_ACTION = 'action';
    public const PARAM_FIRSTLETTER = 'firstletter';
    public const PARAM_GROUP_ID = 'group_id';
    public const PARAM_GROUP_REL_USER_ID = 'group_rel_user_id';
    public const PARAM_USER_ID = 'user_id';

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::CONTEXT);
    }

    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    protected function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    public function get_create_group_url(string $parentGroupIdentifier): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => self::CONTEXT,
                self::PARAM_ACTION => self::ACTION_CREATE_GROUP,
                self::PARAM_GROUP_ID => $parentGroupIdentifier
            ]
        );
    }

    public function get_group_delete_url(Group $group): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => self::CONTEXT,
                self::PARAM_ACTION => self::ACTION_DELETE_GROUP,
                self::PARAM_GROUP_ID => $group->getId()
            ]
        );
    }

    public function get_group_editing_url(Group $group): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => self::CONTEXT,
                self::PARAM_ACTION => self::ACTION_EDIT_GROUP,
                self::PARAM_GROUP_ID => $group->getId()
            ]
        );
    }

    public function get_group_emptying_url(Group $group): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => self::CONTEXT,
                self::PARAM_ACTION => self::ACTION_TRUNCATE_GROUP,
                self::PARAM_GROUP_ID => $group->getId()
            ]
        );
    }

    public function get_group_metadata_url(Group $group): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => self::CONTEXT,
                self::PARAM_ACTION => self::ACTION_MANAGE_METADATA,
                self::PARAM_GROUP_ID => $group->getId()
            ]
        );
    }

    public function get_group_subscribe_user_browser_url(Group $group): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => self::CONTEXT,
                self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER,
                self::PARAM_GROUP_ID => $group->getId()
            ]
        );
    }

    public function get_group_viewing_url(Group $group): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => self::CONTEXT,
                self::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                self::PARAM_GROUP_ID => $group->getId()
            ]
        );
    }
}
