<?php
namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass;

use Chamilo\Core\Repository\Workspace\Extension\Office365\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceOffice365Reference extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_LINKED = 'linked';
    public const PROPERTY_OFFICE365_GROUP_ID = 'office365_group_id';
    public const PROPERTY_OFFICE365_PLAN_ID = 'office365_plan_id';
    public const PROPERTY_WORKSPACE_ID = 'workspace_id';

    /**
     * @param array $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_WORKSPACE_ID;
        $extendedPropertyNames[] = self::PROPERTY_OFFICE365_GROUP_ID;
        $extendedPropertyNames[] = self::PROPERTY_OFFICE365_PLAN_ID;
        $extendedPropertyNames[] = self::PROPERTY_LINKED;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public function getOffice365GroupId()
    {
        return $this->getDefaultProperty(self::PROPERTY_OFFICE365_GROUP_ID);
    }

    /**
     * @return string
     */
    public function getOffice365PlanId()
    {
        return $this->getDefaultProperty(self::PROPERTY_OFFICE365_PLAN_ID);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_workspace_office365_reference';
    }

    /**
     * @return int
     */
    public function getWorkspaceId()
    {
        return $this->getDefaultProperty(self::PROPERTY_WORKSPACE_ID);
    }

    /**
     * Returns whether or not the reference to the office365 is still active. Used to deactivate the connection
     * without loosing the reference information
     *
     * @return bool
     */
    public function isLinked()
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_LINKED);
    }

    /**
     * Stores the linked property. Used to deactivate the connection
     * without loosing the reference information
     *
     * @param bool $linked
     *
     * @return $this
     */
    public function setLinked($linked = true)
    {
        $this->setDefaultProperty(self::PROPERTY_LINKED, $linked);

        return $this;
    }

    /**
     * @param string $office365GroupId
     *
     * @return WorkspaceOffice365Reference
     */
    public function setOffice365GroupId($office365GroupId)
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICE365_GROUP_ID, $office365GroupId);

        return $this;
    }

    /**
     * @param string $office365PlanId
     *
     * @return WorkspaceOffice365Reference
     */
    public function setOffice365PlanId($office365PlanId)
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICE365_PLAN_ID, $office365PlanId);

        return $this;
    }

    /**
     * @param int $workspaceId
     *
     * @return WorkspaceOffice365Reference
     */
    public function setWorkspaceId($workspaceId)
    {
        $this->setDefaultProperty(self::PROPERTY_WORKSPACE_ID, $workspaceId);

        return $this;
    }
}