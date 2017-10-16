<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class parsers xml files which describe a dynamic form and builds the elements
 *
 * @package Chamilo\Libraries\Format\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class XmlFormParser
{
    const ELEMENT_TYPE_TEXT = 'text';
    const ELEMENT_TYPE_SELECT = 'select';
    const ELEMENT_TYPE_CHECKBOX = 'checkbox';
    const ELEMENT_TYPE_TOGGLE = 'toggle';
    const ELEMENT_TYPE_RADIO = 'radio';
    const ELEMENT_TYPE_STATIC = 'static';
    const ELEMENT_TYPE_HTML_EDITOR = 'html_editor';
    const ELEMENT_TYPE_GROUP = 'group';
    const ELEMENT_TYPE_HTML = 'html';

    /**
     * The Formvalidator object which builds the form elements
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $form_builder;

    /**
     * The Xml Form Parser Result object that stores the result of this parsed form
     *
     * @var \Chamilo\Libraries\Format\Form\XmlFormParserResult
     */
    private $xml_form_parser_result;

    /**
     * The XPath object to search in the xml structure
     *
     * @var \DOMXPath
     */
    private $dom_xpath;

    /**
     * The connector class to retrieve the form element options
     *
     * @var Object
     */
    private $connector_class;

    /**
     * The context of the form elements, used for translations
     *
     * @param string
     */
    private $context;

    /**
     * The prefix for the elements
     *
     * @var string
     */
    private $prefix;

    /**
     * Constructor Initializes the form builder
     */
    public function __construct()
    {
        $this->form_builder = new FormValidator();
    }

    /**
     * Parses an xml file to an array of form elements
     *
     * @param string $filePath - The path to the xml file
     * @param string $context - [OPTIONAL] The context - Default common\libraries
     * @param Object $connectorClass - [OPTIONAL] The connector class to retrieve the dynamic options
     * @param string $prefix - [OPTIONAL] The prefix for the elements
     * @return \Chamilo\Libraries\Format\Form\XmlFormParserResult
     */
    public function build_elements($filePath, $context = null, $connectorClass = null, $prefix = null)
    {
        if (! file_exists($filePath))
        {
            throw new \Exception(Translation::get('PathToXmlFileDoesNotExist'));
        }

        if (! $context)
        {
            $context = __NAMESPACE__;
        }

        $this->set_context($context);
        $this->set_connector_class($connectorClass);
        $this->set_prefix($prefix);

        $dom_document = new \DOMDocument();
        $dom_document->load($filePath);

        $this->set_dom_xpath(new \DOMXPath($dom_document));

        $this->parse_categories();

        return $this->get_xml_form_parser_result();
    }

    /**
     * Parses the categories in the xml file
     */
    protected function parse_categories()
    {
        $category_nodes = $this->get_dom_xpath()->query('//category');
        foreach ($category_nodes as $category_node)
        {
            $this->parse_category_node($category_node);
        }
    }

    /**
     * Handles a single category node
     *
     * @param \DOMElement $categoryNode
     */
    protected function parse_category_node(\DOMElement $categoryNode)
    {
        $category_name = $categoryNode->getAttribute('name');
        $category_name = $this->translate($category_name);

        $this->create_and_add_element('category', $category_name);

        $this->parse_elements_for_category($categoryNode);

        $this->create_and_add_element('category');
    }

    /**
     * Parses the elements in the xml file for a given category
     *
     * @param \DOMElement $categoryNode
     */
    protected function parse_elements_for_category(\DOMElement $categoryNode)
    {
        $element_nodes = $this->get_dom_xpath()->query('element', $categoryNode);
        foreach ($element_nodes as $element_node)
        {
            $this->parse_element_node($element_node);
        }
    }

    /**
     * Parses a single element node
     *
     * @param \DOMElement $elementNode
     */
    protected function parse_element_node(\DOMElement $elementNode)
    {
        $element_name = $elementNode->getAttribute('name');
        $element_title = $this->translate($element_name);
        $element_type = $elementNode->getAttribute('field');
        $default_value = $elementNode->getAttribute('default');

        $prefix = $this->get_prefix();

        if ($prefix)
        {
            $element_name = $this->get_prefix() . '[' . $element_name . ']';
        }

        switch ($element_type)
        {
            case self::ELEMENT_TYPE_SELECT :
                $this->create_select_element($elementNode, $element_name, $element_title);
                break;
            case self::ELEMENT_TYPE_RADIO :
                $this->create_radio_element($elementNode, $element_name, $element_title);
                break;
            case self::ELEMENT_TYPE_CHECKBOX :
                $this->create_checkbox_element($elementNode, $element_name, $element_title);
                break;
            case self::ELEMENT_TYPE_TOGGLE :
                $this->create_toggle_element($elementNode, $element_name, $element_title);
                break;
            case self::ELEMENT_TYPE_TEXT :
                $this->create_text_element($elementNode, $element_name, $element_title);
                break;
            default :
                $this->create_and_add_element($element_type, $element_name, $element_title);
                break;
        }

        $this->get_xml_form_parser_result()->add_default_value($element_name, $default_value);

        $this->parse_validation_rules($elementNode, $element_name);
    }

    /**
     * Creates a text element on the form
     *
     * @param \DOMElement $elementNode
     * @param string $elementName
     * @param string $elementTitle
     */
    protected function create_text_element($elementNode, $elementName, $elementTitle)
    {
        $element_size = $elementNode->getAttribute('size');
        if (! $element_size)
        {
            $element_size = 50;
        }

        $attributes = array('size' => $element_size);

        $this->create_and_add_element(self::ELEMENT_TYPE_TEXT, $elementName, $elementTitle, $attributes);
    }

    /**
     * Creates a select element on the form
     *
     * @param \DOMElement $elementNode
     * @param string $elementName
     * @param string $elementTitle
     */
    protected function create_select_element($elementNode, $elementName, $elementTitle)
    {
        $element_options = $this->parse_element_options($elementNode);
        $this->create_and_add_element(self::ELEMENT_TYPE_SELECT, $elementName, $elementTitle, $element_options);
    }

    /**
     * Creates a radio buttons element on the form
     *
     * @param \DOMElement $elementNode
     * @param string $elementName
     * @param string $elementTitle
     */
    protected function create_radio_element($elementNode, $elementName, $elementTitle)
    {
        $element_options = $this->parse_element_options($elementNode);

        $group_elements = array();

        foreach ($element_options as $option_value => $option_name)
        {
            $group_elements[] = $this->form_builder->createElement(
                self::ELEMENT_TYPE_RADIO,
                $elementName,
                null,
                $option_name,
                $option_value);
        }

        $this->create_and_add_element(self::ELEMENT_TYPE_GROUP, $elementName, $elementTitle, $group_elements, '', false);
    }

    /**
     * Creates a checkbox element on the form
     *
     * @param \DOMElement $elementNode
     * @param string $elementName
     * @param string $elementTitle
     */
    protected function create_checkbox_element($elementNode, $elementName, $elementTitle)
    {
        $this->create_and_add_element(self::ELEMENT_TYPE_CHECKBOX, $elementName, $elementTitle, '', null, '1', '0');
    }

    /**
     * Creates a toggle element on the form
     *
     * @param \DOMElement $elementNode
     * @param string $elementName
     * @param string $elementTitle
     */
    protected function create_toggle_element($elementNode, $elementName, $elementTitle)
    {
        $this->create_and_add_element(self::ELEMENT_TYPE_TOGGLE, $elementName, $elementTitle, '', null, '1', '0');
    }

    /**
     * Parses the options for a given element node
     *
     * @param \DOMElement $elementNode
     * @return string[] The options
     */
    protected function parse_element_options(\DOMElement $elementNode)
    {
        $options_node_list = $this->get_dom_xpath()->query('options', $elementNode);
        if ($options_node_list->length == 0)
        {
            return;
        }

        $options_node = $options_node_list->item(0);
        $options_type = $options_node->getAttribute('type');

        if ($options_type == 'dynamic')
        {
            $source = $options_node->getAttribute('source');
            $options = call_user_func(array($this->get_connector_class(), $source));
        }
        else
        {
            $options = array();

            $option_node_list = $this->get_dom_xpath()->query('option', $options_node);
            foreach ($option_node_list as $option_node)
            {
                $option_name = $option_node->getAttribute('name');
                $option_value = $option_node->getAttribute('value');

                $options[$option_value] = $this->translate($option_name);
            }
        }

        return $options;
    }

    /**
     * Parses the validation rules for an element node
     *
     * @param \DOMElement $elementNode
     * @param String $elementName
     */
    protected function parse_validation_rules(\DOMElement $elementNode, $elementName)
    {
        $required_attribute = $elementNode->getAttribute('required');
        if ($required_attribute)
        {
            $this->create_and_add_validation_rule($elementName, Translation::get('ThisFieldIsRequired'), 'required');
        }

        $validation_node_list = $this->get_dom_xpath()->query('validations/validation', $elementNode);
        foreach ($validation_node_list as $validation_node)
        {
            $type = $validation_node->getAttribute('rule');
            $message = $validation_node->getAttribute('message');

            $this->create_and_add_validation_rule($elementName, $message, $type);
        }
    }

    /**
     * Creates an element and adds it to the elements list
     *
     * @param mixed Dynamic Arguments List - Same arguments as in createElement for quickform
     * @return \HTML_QuickForm_element
     */
    protected function create_and_add_element()
    {
        $element = call_user_func_array(array($this->form_builder, 'createElement'), func_get_args());

        $this->get_xml_form_parser_result()->add_element($element);

        return $element;
    }

    /**
     * Creates a validation rule with the given parameters and adds it to the result
     *
     * @param string $element - The form element name
     * @param string $message - The message to display
     * @param string $type - The validation rule type
     * @param string $format - [OPTIONAL] The format - Required for extra rule data
     * @param string $validation - [OPTIONAL] The place to execute the validation Server - Client
     * @param boolean $reset - [OPTIONAL] Whether or not to reset the elements on client validation error
     * @param boolean $force - [OPTIONAL] Forces the rule to be applied, even if the element does not exist yet
     */
    protected function create_and_add_validation_rule($element, $message, $type, $format = null, $validation = 'server',
        $reset = false, $force = false)
    {
        $validation_rule = new XmlFormParserValidationRule(
            $element,
            $message,
            $type,
            $format,
            $validation,
            $reset,
            $force);
        $this->get_xml_form_parser_result()->add_validation_rule($validation_rule);
    }

    /**
     * Translates a variable with the global context variable
     *
     * @param string $translation_variable
     * @param string[] $parameters
     * @return string
     */
    protected function translate($translation_variable, $parameters = [])
    {
        $translation_variable = (string) StringUtilities::getInstance()->createString($translation_variable)->upperCamelize();
        return Translation::get($translation_variable, $parameters, $this->get_context());
    }

    /**
     * Returns a new result object
     *
     * @return \Chamilo\Libraries\Format\Form\XmlFormParserResult
     */
    protected function create_new_xml_form_parser_result()
    {
        return new XmlFormParserResult();
    }

    /**
     * Returns the xml_form_parser_result object
     *
     * @return \Chamilo\Libraries\Format\Form\XmlFormParserResult
     */
    public function get_xml_form_parser_result()
    {
        if (! isset($this->xml_form_parser_result))
        {
            $this->xml_form_parser_result = $this->create_new_xml_form_parser_result();
        }
        return $this->xml_form_parser_result;
    }

    /**
     * Sets the xml_form_parser_result object
     *
     * @param \Chamilo\Libraries\Format\Form\XmlFormParserResult $xml_form_parser_result
     */
    public function set_xml_form_parser_result(XmlFormParserResult $xml_form_parser_result)
    {
        $this->xml_form_parser_result = $xml_form_parser_result;
    }

    /**
     * Returns the form_builder object
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function get_form_builder()
    {
        return $this->form_builder;
    }

    /**
     * Sets the form_builder object
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formBuilder
     */
    public function set_form_builder(FormValidator $formBuilder)
    {
        $this->form_builder = $formBuilder;
    }

    /**
     * Returns the dom_xpath object
     *
     * @return \DOM_XPath
     */
    public function get_dom_xpath()
    {
        return $this->dom_xpath;
    }

    /**
     * Sets the dom_xpath object
     *
     * @param \DOM_XPath $dom_xpath
     */
    public function set_dom_xpath($dom_xpath)
    {
        $this->dom_xpath = $dom_xpath;
    }

    /**
     * Returns the connector_class object
     *
     * @return Object
     */
    public function get_connector_class()
    {
        return $this->connector_class;
    }

    /**
     * Sets the connector_class object
     *
     * @param Object $connectorClass
     */
    public function set_connector_class($connectorClass)
    {
        $this->connector_class = $connectorClass;
    }

    /**
     * Returns the context
     *
     * @return string
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     * Sets the context
     *
     * @param string $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     * Returns the prefix
     *
     * @return string
     */
    public function get_prefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the prefix
     *
     * @param string $prefix
     */
    public function set_prefix($prefix)
    {
        $this->prefix = $prefix;
    }
}
