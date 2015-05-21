<?php
namespace Chamilo\Core\MetadataOld\Ajax\Component;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\MetadataOld\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return the controlled vocabulary
 * 
 * @author Sven Vanpoucke
 * @package core\metadata
 */
class ControlledVocabularyFeedComponent extends \Chamilo\Core\MetadataOld\Ajax\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PARAM_FILTER = 'filter';
    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     * Run the AJAX component
     */
    public function run()
    {
        $result = new JsonAjaxResult();
        
        $elements = $this->get_elements();
        $elements = $elements->as_array();
        
        $result->set_property(self :: PROPERTY_ELEMENTS, $elements);
        $result->set_property(self :: PROPERTY_TOTAL_ELEMENTS, $this->count_controlled_vocabulary());
        
        $result->display();
    }

    /**
     * Returns the elements
     */
    protected function get_elements()
    {
        $search_query = Request :: post(self :: PARAM_SEARCH_QUERY);
        
        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $condition = Utilities :: query_to_condition($search_query, array(ControlledVocabulary :: PROPERTY_VALUE));
        }
        
        $properties = new DataClassRetrievesParameters($condition, 100, $this->get_offset());
        
        $controlled_vocabularies = DataManager :: retrieves(ControlledVocabulary :: class_name(), $properties);
        
        $elements = new AdvancedElementFinderElements();
        
        while ($controlled_vocabulary = $controlled_vocabularies->next_result())
        {
            $elements->add_element(
                new AdvancedElementFinderElement(
                    'controlled_vocabulary_id_' . $controlled_vocabulary->get_id(), 
                    'type', 
                    $controlled_vocabulary->get_value(), 
                    $controlled_vocabulary->get_value()));
        }
        
        return $elements;
    }

    /**
     * Returns the offset value for the retrieves function
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
     * Counts the number of controlled vocabulary
     * 
     * @return int
     */
    protected function count_controlled_vocabulary()
    {
        return DataManager :: count(ControlledVocabulary :: class_name());
    }
}