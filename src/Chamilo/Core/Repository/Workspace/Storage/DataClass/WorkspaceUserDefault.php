<?php
namespace Chamilo\Core\Repository\Workspace\Storage\DataClass;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceUserDefault extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;
    
    public const PROPERTY_USER_ID = 'user_id';
    public const PROPERTY_WORKSPACE_ID = 'workspace_id';

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_USER_ID,
                self::PROPERTY_WORKSPACE_ID
            ]
        );
    }

    public static function getStorageUnitName(): string
    {
        return 'repository_workspace_user_default';
    }

    public function getUserIdentifier(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function getWorkspaceIdentifier(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_WORKSPACE_ID);
    }

    public function setUserIdentifier(string $userIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userIdentifier);
    }

    public function setWorkspaceIdentifier(string $workspaceIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_WORKSPACE_ID, $workspaceIdentifier);
    }

}