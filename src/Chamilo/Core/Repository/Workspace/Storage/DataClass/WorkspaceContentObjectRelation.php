<?php
namespace Chamilo\Core\Repository\Workspace\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceContentObjectRelation extends DataClass
{
    
    // Properties
    const PROPERTY_WORKSPACE_ID = 'workspace_id';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    private $workspace;

    /**
     *
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_WORKSPACE_ID, self::PROPERTY_CATEGORY_ID, self::PROPERTY_CONTENT_OBJECT_ID));
    }

    /**
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY_ID);
    }

    /**
     *
     * @param integer $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY_ID, $categoryId);
    }

    /**
     *
     * @return integer
     */
    public function getWorkspaceId()
    {
        return $this->get_default_property(self::PROPERTY_WORKSPACE_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function getWorkspace()
    {
        if (! isset($this->workspace))
        {
            $this->workspace = DataManager::retrieve_by_id(Workspace::class_name(), $this->getWorkspaceId());
        }
        
        return $this->workspace;
    }

    /**
     *
     * @param integer $workspace_id
     */
    public function setWorkspaceId($workspaceId)
    {
        $this->set_default_property(self::PROPERTY_WORKSPACE_ID, $workspaceId);
    }

    /**
     *
     * @return integer
     */
    public function getContentObjectId()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        if (! isset($this->contentObject))
        {
            $this->contentObject = DataManager::retrieve_by_id(ContentObject::class, $this->getContentObjectId());
        }
        
        return $this->contentObject;
    }

    /**
     *
     * @param integer $content_object_id
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);
    }
}