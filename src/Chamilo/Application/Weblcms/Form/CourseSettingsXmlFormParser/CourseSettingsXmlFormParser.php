<?php
namespace Chamilo\Application\Weblcms\Form\CourseSettingsXmlFormParser;

use Chamilo\Application\Weblcms\Interfaces\CourseSettingsXmlFormParserSupport;
use Chamilo\Libraries\Format\Form\SettingsXmlFormParser;

/**
 * This class extends the common settings xml form parser to parse course settings with locked and frozen elements
 * 
 * @package \application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSettingsXmlFormParser extends SettingsXmlFormParser
{

    /**
     * The parent form that runs this parser and supports
     * 
     * @var CourseSettingsXmlFormParserSupport
     */
    private $parent;

    /**
     * The tool for which the settings are parsed
     * 
     * @var int
     */
    private $tool_id;

    /**
     * A list of the elements that need to be frozen
     * 
     * @var string[]
     */
    private $frozen_elements = [];

    /**
     * **************************************************************************************************************
     * Inherited functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param $form SettingsXmlFormParserSupport
     * @param $tool_id int - [OPTIONAL] default 0
     */
    public function __construct(CourseSettingsXmlFormParserSupport $parent, $tool_id = 0)
    {
        if (! $parent instanceof CourseSettingsXmlFormParserSupport)
        {
            throw new \Exception(
                'The parent of the CourseSettingsXmlFormParser object should be an instance of
                CourseSettingsXmlFormParserSupport');
        }
        
        parent::__construct();
        
        $this->parent = $parent;
        $this->tool_id = $tool_id;
    }

    /**
     * Parses a single element node
     * 
     * @param $element_node \DOMElement
     */
    protected function parse_element_node(\DOMElement $element_node)
    {
        $element_name = $element_node->getAttribute('name');
        
        if (! $this->parent->can_change_course_setting($element_name, $this->tool_id))
        {
            $prefix = $this->get_prefix();
            
            if ($prefix)
            {
                $element_name = $this->get_prefix() . '[' . $element_name . ']';
            }
            
            $this->frozen_elements[] = $element_name;
        }
        
        parent::parse_element_node($element_node);
    }

    /**
     * Returns a new result object
     * 
     * @return XmlFormParserResult
     */
    protected function create_new_xml_form_parser_result()
    {
        return new CourseSettingsXmlFormParserResult($this);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns whether or not a given element is frozen
     * 
     * @param $element_name string
     *
     * @return boolean
     */
    public function is_element_frozen($element_name)
    {
        return in_array($element_name, $this->frozen_elements);
    }
}
