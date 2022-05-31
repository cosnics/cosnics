<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;

class ExportParameters
{

    private $content_object_ids = [];

    private $category_ids = [];

    private $type = ContentObjectExport::TYPE_DEFAULT;

    private $format = ContentObjectExport::FORMAT_CPO;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    private $user;

    private $category_content_object_ids;

    public function __construct(WorkspaceInterface $workspace, $user, $format = ContentObjectExport::FORMAT_CPO, $content_object_ids = [],
        $category_ids = [], $type = ContentObjectExport::TYPE_DEFAULT)
    {
        $this->workspace = $workspace;
        $this->user = $user;
        $this->type = $type;
        $this->content_object_ids = $content_object_ids;
        $this->category_ids = $category_ids;
        $this->format = $format;

        if (count($content_object_ids) > 0 && count($category_ids) > 0)
        {
            throw new Exception(Translation::get('ChooseContentObjectsOrCategories'));
        }
    }

    /**
     *
     * @return the $content_object_ids
     */
    public function get_content_object_ids()
    {
        if (count($this->get_category_ids()) > 0)
        {
            if (! isset($this->category_content_object_ids))
            {
                if (in_array(0, $this->get_category_ids()))
                {
                    // if (! RightsService::getInstance()->canCopyContentObjects(
                    // $this->get_user(),
                    // $this->getWorkspace()))
                    // {
                    // return [];
                    // }

                    if ($this->getWorkspace() instanceof PersonalWorkspace)
                    {
                        $conditions = [];
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
                            new StaticConditionVariable($this->get_user()));
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
                            new StaticConditionVariable(ContentObject::STATE_NORMAL));

                        $condition = new AndCondition($conditions);

                        $parameters = new DataClassDistinctParameters(
                            $condition,
                            new RetrieveProperties(
                                array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID))));
                    }
                    else
                    {
                        $joins = new Joins();
                        $joins->add(
                            new Join(
                                WorkspaceContentObjectRelation::class,
                                new EqualityCondition(
                                    new PropertyConditionVariable(
                                        WorkspaceContentObjectRelation::class,
                                        WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID),
                                    new PropertyConditionVariable(
                                        ContentObject::class,
                                        ContentObject::PROPERTY_OBJECT_NUMBER))));

                        $condition = new EqualityCondition(
                            new PropertyConditionVariable(
                                WorkspaceContentObjectRelation::class,
                                WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID),
                            new StaticConditionVariable($this->getWorkspace()->getId()));

                        $parameters = new DataClassDistinctParameters(
                            $condition,
                            new RetrieveProperties(
                                array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID))),
                            $joins);
                    }
                }
                else
                {
                    $category_ids = [];

                    foreach ($this->get_category_ids() as $category_id)
                    {
                        $category = DataManager::retrieve_by_id(RepositoryCategory::class, $category_id);

                        $category_ids[] = $category_id;
                        $category_ids = array_merge($category_ids, $category->get_children_ids());
                    }

                    if ($this->getWorkspace() instanceof PersonalWorkspace)
                    {
                        $conditions = [];
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
                            new StaticConditionVariable($this->get_user()));
                        $conditions[] = new InCondition(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_PARENT_ID),
                            $category_ids);
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
                            new StaticConditionVariable(ContentObject::STATE_NORMAL));

                        $condition = new AndCondition($conditions);

                        $parameters = new DataClassDistinctParameters(
                            $condition,
                            new RetrieveProperties(
                                array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID))));
                    }
                    else
                    {
                        $joins = new Joins();
                        $joins->add(
                            new Join(
                                WorkspaceContentObjectRelation::class,
                                new EqualityCondition(
                                    new PropertyConditionVariable(
                                        WorkspaceContentObjectRelation::class,
                                        WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID),
                                    new PropertyConditionVariable(
                                        ContentObject::class,
                                        ContentObject::PROPERTY_OBJECT_NUMBER))));

                        $conditions = [];

                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                WorkspaceContentObjectRelation::class,
                                WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID),
                            new StaticConditionVariable($this->getWorkspace()->getId()));

                        $conditions[] = new InCondition(
                            new PropertyConditionVariable(
                                WorkspaceContentObjectRelation::class,
                                WorkspaceContentObjectRelation::PROPERTY_CATEGORY_ID),
                            $category_ids);

                        $condition = new AndCondition($conditions);

                        $parameters = new DataClassDistinctParameters(
                            $condition,
                            new RetrieveProperties(
                                array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID))),
                            $joins);
                    }
                }

                $this->category_content_object_ids = DataManager::distinct(ContentObject::class, $parameters);
            }

            return $this->category_content_object_ids;
        }
        else
        {
            $checkedContentObjects = [];

            foreach ($this->content_object_ids as $contentObjectIdentifier)
            {
                $contentObject = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                    ContentObject::class,
                    $contentObjectIdentifier);

                // if (RightsService::getInstance()->canCopyContentObject(
                // $this->get_user(),
                // $contentObject,
                // $this->getWorkspace()))
                // {
                $checkedContentObjects[] = $contentObjectIdentifier;
                // }

                $this->content_object_ids = $checkedContentObjects;
            }

            return $this->content_object_ids;
        }
    }

    /**
     *
     * @param $content_object_ids
     */
    public function set_content_object_ids($content_object_ids)
    {
        $this->content_object_ids = $content_object_ids;
    }

    /**
     *
     * @return the $category_ids
     */
    public function get_category_ids()
    {
        return $this->category_ids;
    }

    /**
     *
     * @param $category_ids
     */
    public function set_category_ids($category_ids)
    {
        $this->category_ids = $category_ids;
    }

    /**
     *
     * @deprecated Use ExportParameters::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param $type string
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return the $user
     */
    public function get_user()
    {
        return $this->user;
    }

    /**
     *
     * @param $user string
     */
    public function set_user($user)
    {
        $this->user = $user;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     *
     * @return the $format
     */
    public function get_format()
    {
        return $this->format;
    }

    /**
     *
     * @param $format string
     */
    public function set_format($format)
    {
        $this->format = $format;
    }

    public function has_categories()
    {
        return count($this->get_category_ids()) > 0;
    }
}
