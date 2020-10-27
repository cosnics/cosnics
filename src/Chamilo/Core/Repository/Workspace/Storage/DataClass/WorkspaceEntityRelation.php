<?php
namespace Chamilo\Core\Repository\Workspace\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceEntityRelation extends DataClass
{
    
    // Properties
    const PROPERTY_WORKSPACE_ID = 'workspace_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_RIGHTS = 'rights';

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    private $workspace;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_WORKSPACE_ID, 
                self::PROPERTY_ENTITY_TYPE, 
                self::PROPERTY_ENTITY_ID, 
                self::PROPERTY_RIGHTS));
    }

    /**
     *
     * @return integer
     */
    public function get_workspace_id()
    {
        return $this->get_default_property(self::PROPERTY_WORKSPACE_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function get_workspace()
    {
        if (! isset($this->workspace))
        {
            $this->workspace = DataManager::retrieve_by_id(Workspace::class, $this->get_workspace_id());
        }
        
        return $this->workspace;
    }

    /**
     *
     * @param integer $workspace_id
     */
    public function set_workspace_id($workspace_id)
    {
        $this->set_default_property(self::PROPERTY_WORKSPACE_ID, $workspace_id);
    }

    /**
     *
     * @return string
     */
    public function get_entity_type()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param string $entity_type
     */
    public function set_entity_type($entity_type)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    /**
     *
     * @return integer
     */
    public function get_entity_id()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    /**
     *
     * @param integer $entity_id
     */
    public function set_entity_id($entity_id)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    /**
     *
     * @return integer
     */
    public function get_rights()
    {
        return $this->get_default_property(self::PROPERTY_RIGHTS);
    }

    /**
     *
     * @param integer $rights
     */
    public function set_rights($rights)
    {
        $this->set_default_property(self::PROPERTY_RIGHTS, $rights);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_workspace_entity_relation';
    }
}