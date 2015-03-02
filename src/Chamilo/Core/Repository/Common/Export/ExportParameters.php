<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

class ExportParameters
{
    const CLASS_NAME = __CLASS__;

    private $content_object_ids = array();

    private $category_ids = array();

    private $type = ContentObjectExport :: TYPE_DEFAULT;

    private $format = ContentObjectExport :: FORMAT_CPO;

    private $user;

    private $category_content_object_ids;

    public function __construct($user, $format = ContentObjectExport :: FORMAT_CPO, $content_object_ids = array(), $category_ids = array(), 
        $type = ContentObjectExport :: TYPE_DEFAULT)
    {
        $this->user = $user;
        $this->type = $type;
        $this->content_object_ids = $content_object_ids;
        $this->category_ids = $category_ids;
        $this->format = $format;
        
        if (count($content_object_ids) > 0 && count($category_ids) > 0)
        {
            throw new Exception(Translation :: get('ChooseContentObjectsOrCategories'));
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
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
                        new StaticConditionVariable($this->get_user()));
                }
                else
                {
                    $category_ids = array();
                    foreach ($this->get_category_ids() as $category_id)
                    {
                        $category = DataManager :: retrieve_by_id(RepositoryCategory :: class_name(), $category_id);
                        $category_ids[] = $category_id;
                        $category_ids = array_merge($category_ids, $category->get_children_ids());
                    }
                    
                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
                        new StaticConditionVariable($this->get_user()));
                    $conditions[] = new InCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_PARENT_ID), 
                        $category_ids);
                    $condition = new AndCondition($conditions);
                }
                
                $parameters = new DataClassDistinctParameters($condition, ContentObject :: PROPERTY_ID);
                $this->category_content_object_ids = DataManager :: distinct(ContentObject :: class_name(), $parameters);
            }
            return $this->category_content_object_ids;
        }
        else
        {
            return $this->content_object_ids;
        }
    }

    /**
     *
     * @param $content_object_ids multitype:
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
     * @param $category_ids multitype:
     */
    public function set_category_ids($category_ids)
    {
        $this->category_ids = $category_ids;
    }

    /**
     *
     * @return the $type
     */
    public function get_type()
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
