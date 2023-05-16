<?php
namespace Chamilo\Core\Repository\Workspace\Storage\DataClass;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceEntityRelation extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ENTITY_ID = 'entity_id';
    public const PROPERTY_ENTITY_TYPE = 'entity_type';
    public const PROPERTY_RIGHTS = 'rights';
    public const PROPERTY_WORKSPACE_ID = 'workspace_id';

    /**
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    private $workspace;

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_WORKSPACE_ID,
                self::PROPERTY_ENTITY_TYPE,
                self::PROPERTY_ENTITY_ID,
                self::PROPERTY_RIGHTS
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_workspace_entity_relation';
    }

    /**
     * @return int
     */
    public function get_entity_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    /**
     * @return string
     */
    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * @return int
     */
    public function get_rights()
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHTS);
    }

    /**
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function get_workspace()
    {
        if (!isset($this->workspace))
        {
            $this->workspace = DataManager::retrieve_by_id(Workspace::class, $this->get_workspace_id());
        }

        return $this->workspace;
    }

    /**
     * @return int
     */
    public function get_workspace_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_WORKSPACE_ID);
    }

    /**
     * @param int $entity_id
     */
    public function set_entity_id($entity_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    /**
     * @param string $entity_type
     */
    public function set_entity_type($entity_type)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    /**
     * @param int $rights
     */
    public function set_rights($rights)
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHTS, $rights);
    }

    /**
     * @param int $workspace_id
     */
    public function set_workspace_id($workspace_id)
    {
        $this->setDefaultProperty(self::PROPERTY_WORKSPACE_ID, $workspace_id);
    }
}