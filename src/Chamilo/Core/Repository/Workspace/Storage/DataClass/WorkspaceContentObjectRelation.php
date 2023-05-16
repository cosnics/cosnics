<?php
namespace Chamilo\Core\Repository\Workspace\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 * @package Chamilo\Core\Repository\Workspace\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceContentObjectRelation extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CATEGORY_ID = 'category_id';
    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    public const PROPERTY_WORKSPACE_ID = 'workspace_id';

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    private $workspace;

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getDefaultProperty(self::PROPERTY_CATEGORY_ID);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        if (!isset($this->contentObject))
        {
            $this->contentObject = DataManager::retrieve_by_id(ContentObject::class, $this->getContentObjectId());
        }

        return $this->contentObject;
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_WORKSPACE_ID, self::PROPERTY_CATEGORY_ID, self::PROPERTY_CONTENT_OBJECT_ID]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_workspace_content_object_relation';
    }

    /**
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function getWorkspace()
    {
        if (!isset($this->workspace))
        {
            $this->workspace = DataManager::retrieve_by_id(Workspace::class, $this->getWorkspaceId());
        }

        return $this->workspace;
    }

    /**
     * @return int
     */
    public function getWorkspaceId()
    {
        return $this->getDefaultProperty(self::PROPERTY_WORKSPACE_ID);
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->setDefaultProperty(self::PROPERTY_CATEGORY_ID, $categoryId);
    }

    /**
     * @param int $content_object_id
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);
    }

    /**
     * @param int $workspace_id
     */
    public function setWorkspaceId($workspaceId)
    {
        $this->setDefaultProperty(self::PROPERTY_WORKSPACE_ID, $workspaceId);
    }
}