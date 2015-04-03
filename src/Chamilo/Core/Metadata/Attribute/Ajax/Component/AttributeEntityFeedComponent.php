<?php
namespace Chamilo\Core\Metadata\Attribute\Ajax;

use Chamilo\Core\Metadata\Attribute\Entity\AttributeEntity;
use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Metadata\Attribute\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return users from the user entity
 * 
 * @package rights
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttributeEntityFeedComponent extends \Chamilo\Core\Metadata\Attribute\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';
    const PROPERTY_ELEMENTS = 'elements';

    private $attribute_count = 0;

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();
        $result->set_property(self :: PROPERTY_ELEMENTS, $this->get_elements()->as_array());
        $result->set_property(self :: PROPERTY_TOTAL_ELEMENTS, $this->attribute_count);
        $result->display();
    }

    /**
     * Returns all the elements for this feed
     * 
     * @return AdvancedElementFinderElements
     */
    private function get_elements()
    {
        $advanced_element_finder_elements = new AdvancedElementFinderElements();
        
        // Add user category
        $attribute_category = new AdvancedElementFinderElement(
            'attributes', 
            'category', 
            Translation :: get('Attributes'), 
            Translation :: get('Attributes'));
        $advanced_element_finder_elements->add_element($attribute_category);
        
        $attributes = $this->retrieve_attributes();
        
        if ($attributes)
        {
            while ($attribute = $attributes->next_result())
            {
                $attribute_category->add_child($this->get_element_for_attribute($attribute));
            }
        }
        
        return $advanced_element_finder_elements;
    }

    /**
     * Retrieves the users from the course (direct subscribed and group subscribed)
     * 
     * @return ResultSet
     */
    public function retrieve_attributes()
    {
        $search_query = Request :: post(self :: PARAM_SEARCH_QUERY);
        
        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities :: query_to_condition($search_query, array(Attribute :: PROPERTY_NAME));
        }
        
        // Combine the conditions
        $count = count($conditions);
        if ($count > 1)
        {
            $condition = new AndCondition($conditions);
        }
        
        if ($count == 1)
        {
            $condition = $conditions[0];
        }
        
        $this->attribute_count = DataManager :: count(Attribute :: class_name(), $condition);
        
        $parameters = new DataClassRetrievesParameters(
            $condition, 
            100, 
            $this->get_offset(), 
            array(
                new OrderBy(new PropertyConditionVariable(Attribute :: class_name(), Attribute :: PROPERTY_SCHEMA_ID)), 
                new OrderBy(new PropertyConditionVariable(Attribute :: class_name(), Attribute :: PROPERTY_NAME))));
        
        return DataManager :: retrieves(Attribute :: class_name(), $parameters);
    }

    /**
     * Returns the selected offset
     * 
     * @return int
     */
    protected function get_offset()
    {
        $offset = Request :: post(self :: PARAM_OFFSET);
        
        if (! isset($offset) || is_null($offset))
        {
            $offset = 0;
        }
        
        return $offset;
    }

    /**
     * Returns the advanced element finder element for the given user
     * 
     * @param Attribute $attribute
     *
     * @return AdvancedElementFinderElement
     */
    protected function get_element_for_attribute($attribute)
    {
        return new AdvancedElementFinderElement(
            AttributeEntity :: ENTITY_TYPE . '_' . $attribute->get_id(), 
            'type type_attribute', 
            $attribute->get_namespace() . ':' . $attribute->get_name(), 
            $attribute->get_namespace() . ':' . $attribute->get_name());
    }

    public function set_attribute_count($attribute_count)
    {
        $this->attribute_count = $attribute_count;
    }
}
