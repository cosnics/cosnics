<?php
namespace Chamilo\Libraries\Format\Form;

use DOMElement;

/**
 * This class extends the xml form parser and is needed because the settings xml file define their elements as setting
 * nodes
 *
 * @package Chamilo\Libraries\Format\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SettingsXmlFormParser extends XmlFormParser
{

    /**
     * Parses the elements in the xml file for a given category
     *
     * @param \DOMElement $categoryNode
     */
    protected function parse_elements_for_category(DOMElement $categoryNode)
    {
        $element_nodes = $this->get_dom_xpath()->query('setting', $categoryNode);
        foreach ($element_nodes as $element_node)
        {
            $this->parse_element_node($element_node);
        }
    }
}
