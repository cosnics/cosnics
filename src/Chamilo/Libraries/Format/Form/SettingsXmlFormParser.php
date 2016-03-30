<?php
namespace Chamilo\Libraries\Format\Form;

/**
 * This class extends the xml form parser and is needed because the settings xml file define their elements as setting
 * nodes
 * 
 * @package \libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SettingsXmlFormParser extends XmlFormParser
{

    /**
     * Parses the elements in the xml file for a given category
     * 
     * @param \DOMElement $category_node
     */
    protected function parse_elements_for_category(\DOMElement $category_node)
    {
        $element_nodes = $this->get_dom_xpath()->query('setting', $category_node);
        foreach ($element_nodes as $element_node)
        {
            $this->parse_element_node($element_node);
        }
    }
}
