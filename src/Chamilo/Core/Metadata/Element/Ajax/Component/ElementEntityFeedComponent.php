<?php
namespace Chamilo\Core\Metadata\Element\Ajax\Component;

use Chamilo\Core\Metadata\Element\Entity\ElementEntity;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return users from the user entity
 * 
 * @package rights
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementEntityFeedComponent extends \Chamilo\Core\Metadata\Element\Ajax\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PARAM_ELEMENT_ID = 'element_id';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';
    const PROPERTY_ELEMENTS = 'elements';

    private $element_count = 0;

    /**
     * Returns the required parameters
     * 
     * @return string[]
     */
    public function getRequiredPostParameters()
    {
        return array();
    }

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();
        $result->set_property(self :: PROPERTY_ELEMENTS, $this->get_elements()->as_array());
        $result->set_property(self :: PROPERTY_TOTAL_ELEMENTS, $this->element_count);
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
        $element_category = new AdvancedElementFinderElement(
            'elements', 
            'category', 
            Translation :: get('Elements'), 
            Translation :: get('Elements'));
        $advanced_element_finder_elements->add_element($element_category);
        
        $elements = $this->retrieve_elements();
        
        if ($elements)
        {
            while ($element = $elements->next_result())
            {
                $element_category->add_child($this->get_element_for_element($element));
            }
        }
        
        return $advanced_element_finder_elements;
    }

    /**
     * Retrieves the users from the course (direct subscribed and group subscribed)
     * 
     * @return ResultSet
     */
    public function retrieve_elements()
    {
        $search_query = Request :: post(self :: PARAM_SEARCH_QUERY);
        $element_id = Request :: post(Manager :: PARAM_ELEMENT_ID);
        
        $conditions = array();
        
        if ($element_id)
        {
            $excluded_ids = DataManager :: retrieve_parent_element_ids($element_id);
            
            $excluded_ids[] = $element_id;
            
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                    $excluded_ids));
        }
        
        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities :: query_to_condition($search_query, array(Element :: PROPERTY_NAME));
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
        
        $this->element_count = DataManager :: count(Element :: class_name(), $condition);
        
        $parameters = new DataClassRetrievesParameters(
            $condition, 
            100, 
            $this->get_offset(), 
            array(
                new OrderBy(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID)), 
                new OrderBy(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_NAME))));
        
        return DataManager :: retrieves(Element :: class_name(), $parameters);
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
     * @param Element $element
     *
     * @return AdvancedElementFinderElement
     */
    protected function get_element_for_element($element)
    {
        return new AdvancedElementFinderElement(
            ElementEntity :: ENTITY_TYPE . '_' . $element->get_id(), 
            'type type_element', 
            $element->get_namespace() . ':' . $element->get_name(), 
            $element->get_namespace() . ':' . $element->get_name());
    }

    public function set_element_count($element_count)
    {
        $this->element_count = $element_count;
    }
}
