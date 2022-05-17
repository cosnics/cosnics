<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceUserFavourite extends DataClass
{

    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_WORKSPACE_ID = 'workspace_id';

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    private $workspace;

    /**
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_WORKSPACE_ID, self::PROPERTY_USER_ID));
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_workspace_user_favourite';
    }

    /**
     *
     * @return integer
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
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
     *
     * @return integer
     */
    public function get_workspace_id()
    {
        return $this->get_default_property(self::PROPERTY_WORKSPACE_ID);
    }

    /**
     *
     * @param integer $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @param integer $workspace_id
     */
    public function set_workspace_id($workspace_id)
    {
        $this->set_default_property(self::PROPERTY_WORKSPACE_ID, $workspace_id);
    }
}