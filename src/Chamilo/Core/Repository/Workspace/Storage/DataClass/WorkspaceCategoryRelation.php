<?php
namespace Chamilo\Core\Repository\Workspace\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceCategoryRelation extends DataClass
{
    const CLASS_NAME = __CLASS__;

    // Properties
    const PROPERTY_WORKSPACE_ID = 'workspace_id';
    const PROPERTY_CATEGORY_ID = 'category_id';

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    private $workspace;

    /**
     *
     * @var \Chamilo\Core\Repository\Storage\DataClass\Category
     */
    private $category;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_WORKSPACE_ID, self :: PROPERTY_CATEGORY_ID));
    }

    /**
     *
     * @return int
     */
    public function getWorkspaceId()
    {
        return $this->get_default_property(self :: PROPERTY_WORKSPACE_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function getWorkspace()
    {
        if (! isset($this->workspace))
        {
            $this->workspace = DataManager :: retrieve_by_id(Workspace :: class_name(), $this->getWorkspaceId());
        }

        return $this->workspace;
    }

    /**
     *
     * @param int $workspace_id
     */
    public function setWorkspaceId($workspaceId)
    {
        $this->set_default_property(self :: PROPERTY_WORKSPACE_ID, $workspaceId);
    }

    /**
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory
     */
    public function getCategory()
    {
        if (! isset($this->category))
        {
            $this->category = DataManager :: retrieve_by_id(RepositoryCategory :: class_name(), $this->getCategoryId());
        }

        return $this->category;
    }

    /**
     *
     * @param integer $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY_ID, $categoryId);
    }
}