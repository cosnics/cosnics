<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceOffice365Reference extends DataClass
{
    const PROPERTY_WORKSPACE_ID = 'workspace_id';
    const PROPERTY_OFFICE365_GROUP_ID = 'office365_group_id';
    const PROPERTY_OFFICE365_PLAN_ID = 'office365_plan_id';
    const PROPERTY_LINKED = 'linked';

    /**
     * @param array $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_WORKSPACE_ID;
        $extended_property_names[] = self::PROPERTY_OFFICE365_GROUP_ID;
        $extended_property_names[] = self::PROPERTY_OFFICE365_PLAN_ID;
        $extended_property_names[] = self::PROPERTY_LINKED;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * @return int
     */
    public function getWorkspaceId()
    {
        return $this->get_default_property(self::PROPERTY_WORKSPACE_ID);
    }

    /**
     * @param int $workspaceId
     *
     * @return WorkspaceOffice365Reference
     */
    public function setWorkspaceId($workspaceId)
    {
        $this->set_default_property(self::PROPERTY_WORKSPACE_ID, $workspaceId);

        return $this;
    }

    /**
     * @return string
     */
    public function getOffice365GroupId()
    {
        return $this->get_default_property(self::PROPERTY_OFFICE365_GROUP_ID);
    }

    /**
     * @param string $office365GroupId
     *
     * @return WorkspaceOffice365Reference
     */
    public function setOffice365GroupId($office365GroupId)
    {
        $this->set_default_property(self::PROPERTY_OFFICE365_GROUP_ID, $office365GroupId);

        return $this;
    }

    /**
     * @return string
     */
    public function getOffice365PlanId()
    {
        return $this->get_default_property(self::PROPERTY_OFFICE365_PLAN_ID);
    }

    /**
     * @param string $office365PlanId
     *
     * @return WorkspaceOffice365Reference
     */
    public function setOffice365PlanId($office365PlanId)
    {
        $this->set_default_property(self::PROPERTY_OFFICE365_PLAN_ID, $office365PlanId);

        return $this;
    }

    /**
     * Returns whether or not the reference to the office365 is still active. Used to deactivate the connection
     * without loosing the reference information
     *
     * @return bool
     */
    public function isLinked()
    {
        return (bool) $this->get_default_property(self::PROPERTY_LINKED);
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
        $this->set_default_property(self::PROPERTY_LINKED, $linked);

        return $this;
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_workspace_office365_reference';
    }
}