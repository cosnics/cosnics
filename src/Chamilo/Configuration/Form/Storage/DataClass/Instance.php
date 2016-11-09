<?php
namespace Chamilo\Configuration\Form\Storage\DataClass;

use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Instance extends DataClass
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_APPLICATION = 'application';

    private $elements;

    public function __construct($defaultProperties)
    {
        parent :: __construct($defaultProperties);

        // $this->elements = array();
    }

    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    public function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    public function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    public function get_elements()
    {
        if (! $this->elements)
            $this->load_elements();

        return $this->elements;
    }

    public function get_element($index)
    {
        return $this->elements[$index];
    }

    public function set_elements($elements)
    {
        $this->elements = $elements;
    }

    public function add_elements($elements)
    {
        if (! is_array($elements))
        {
            $elements = array($elements);
        }

        foreach ($elements as $element)
        {
            $this->elements[] = $element;
        }
    }

    public function load_elements()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($this->get_id()));
        $elements = DataManager :: retrieve_dynamic_form_elements($condition);
        $this->set_elements($elements->as_array());

        return $this->elements;
    }

    /**
     * Get the default properties of all user course categories.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_APPLICATION));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }
}
