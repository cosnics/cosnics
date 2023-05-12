<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
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

    private $category_content_object_ids;

    private $category_ids = [];

    private $content_object_ids = [];

    private $format = ContentObjectExport::FORMAT_CPO;

    private $type = ContentObjectExport::TYPE_DEFAULT;

    private $user;

    private Workspace $workspace;

    public function __construct(
        Workspace $workspace, $user, $format = ContentObjectExport::FORMAT_CPO, $content_object_ids = [],
        $category_ids = [], $type = ContentObjectExport::TYPE_DEFAULT
    )
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    /**
     * @return the $category_ids
     */
    public function get_category_ids()
    {
        return $this->category_ids;
    }

    /**
     * @return the $content_object_ids
     */
    public function get_content_object_ids()
    {
        if (count($this->get_category_ids()) > 0)
        {
            if (!isset($this->category_content_object_ids))
            {
                if (in_array(0, $this->get_category_ids()))
                {
                    $joins = new Joins();
                    $joins->add(
                        new Join(
                            WorkspaceContentObjectRelation::class, new EqualityCondition(
                                new PropertyConditionVariable(
                                    WorkspaceContentObjectRelation::class,
                                    WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
                                ), new PropertyConditionVariable(
                                    ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER
                                )
                            )
                        )
                    );

                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
                        ), new StaticConditionVariable($this->getWorkspace()->getId())
                    );

                    $parameters = new DataClassDistinctParameters(
                        $condition, new RetrieveProperties(
                        [new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)]
                    ), $joins
                    );
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

                    $joins = new Joins();
                    $joins->add(
                        new Join(
                            WorkspaceContentObjectRelation::class, new EqualityCondition(
                                new PropertyConditionVariable(
                                    WorkspaceContentObjectRelation::class,
                                    WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
                                ), new PropertyConditionVariable(
                                    ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER
                                )
                            )
                        )
                    );

                    $conditions = [];

                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
                        ), new StaticConditionVariable($this->getWorkspace()->getId())
                    );

                    $conditions[] = new InCondition(
                        new PropertyConditionVariable(
                            WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CATEGORY_ID
                        ), $category_ids
                    );

                    $condition = new AndCondition($conditions);

                    $parameters = new DataClassDistinctParameters(
                        $condition, new RetrieveProperties(
                        [new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)]
                    ), $joins
                    );
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
                $checkedContentObjects[] = $contentObjectIdentifier;

                $this->content_object_ids = $checkedContentObjects;
            }

            return $this->content_object_ids;
        }
    }

    /**
     * @return the $format
     */
    public function get_format()
    {
        return $this->format;
    }

    /**
     * @deprecated Use ExportParameters::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @return the $user
     */
    public function get_user()
    {
        return $this->user;
    }

    public function has_categories()
    {
        return count($this->get_category_ids()) > 0;
    }

    public function setWorkspace(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     * @param $category_ids
     */
    public function set_category_ids($category_ids)
    {
        $this->category_ids = $category_ids;
    }

    /**
     * @param $content_object_ids
     */
    public function set_content_object_ids($content_object_ids)
    {
        $this->content_object_ids = $content_object_ids;
    }

    /**
     * @param $format string
     */
    public function set_format($format)
    {
        $this->format = $format;
    }

    /**
     * @param $type string
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     * @param $user string
     */
    public function set_user($user)
    {
        $this->user = $user;
    }
}
