<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 *
 * @package Chamilo\Core\Home\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Element extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_TYPE = 'type';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CONFIGURATION = 'configuration';

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent :: __construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_TYPE,
                self :: PROPERTY_PARENT_ID,
                self :: PROPERTY_TITLE,
                self :: PROPERTY_SORT,
                self :: PROPERTY_USER_ID, self :: PROPERTY_CONFIGURATION));
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     *
     * @param integer $parentId
     */
    public function setParentId($parentId)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parentId);
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     *
     * @param integer $sort
     */
    public function setSort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $userId);
    }

    /**
     *
     * @return integer
     */
    public function getConfiguration()
    {
        return $this->get_default_property(self :: PROPERTY_CONFIGURATION);
    }

    /**
     *
     * @param integer $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->set_default_property(self :: PROPERTY_CONFIGURATION, $configuration);
    }

    public function delete()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(static :: class_name(), static :: PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->get_id()));
        $childElements = DataManager :: retrieves(Block :: class_name(), $condition);

        while ($childElement = $childElements->next_result())
        {
            if (! $childElement->delete())
            {
                return false;
            }
        }

        return parent :: delete();
    }

    public function hasChildren()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(static :: class_name(), self :: PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->get_id()));

        $childCount = DataManager :: count(Block :: class_name(), new DataClassCountParameters($condition));

        return ($childCount == 0);
    }

    public function get_display_order_property()
    {
        return new PropertyConditionVariable(static :: class_name(), self :: PROPERTY_SORT);
    }

    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(static :: class_name(), self :: PROPERTY_PARENT_ID));
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[string]
     */
    protected function get_dependencies()
    {
        return array(
            ElementConfiguration :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(
                    ElementConfiguration :: class_name(),
                    ElementConfiguration :: PROPERTY_ELEMENT_ID),
                new StaticConditionVariable($this->get_id())));
    }
}