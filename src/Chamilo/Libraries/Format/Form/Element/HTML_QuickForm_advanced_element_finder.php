<?php

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Advanced ajax based element finder.
 * Includes multiple entities, advanced filtering, multiple selects
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Sven Vanpoucke
 */
class HTML_QuickForm_advanced_element_finder extends HTML_QuickForm_group
{
    const CONFIG_MAX_SELECTABLE_ITEMS = 'maxSelectableItems';

    const DEFAULT_HEIGHT = 300;

    const DEFAULT_WIDTH = 292;

    /**
     * Height of this element
     *
     * @var integer
     */
    private $height;

    /**
     * Width of the element
     *
     * @var integer
     */
    private $width;

    /**
     * List of types of elements on which can be searched
     *
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes
     */
    private $element_types;

    /**
     * List of default selected elements
     *
     * @var \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements
     */
    private $default_values;

    /**
     * An array of configuration values for the elementfinder (eg.
     * max number of selectable items)
     *
     * @var string[]
     */
    private $configuration;

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes $elementTypes
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements $defaultValues
     * @param string[] $config
     */
    public function __construct(
        $elementName = null, $elementLabel = null, $elementTypes = null, $defaultValues = null, $config = array()
    )
    {
        HTML_QuickForm_element::__construct($elementName, $elementLabel);

        $this->configuration = $config;
        $this->_type = 'advanced_element_finder';
        $this->_persistantFreeze = true;
        $this->_appendName = false;

        $this->element_types = $elementTypes;

        $this->height = self::DEFAULT_HEIGHT;
        $this->width = self::DEFAULT_WIDTH;

        if (!empty($elementTypes))
        {
            $this->build_elements();
        }

        $this->setDefaultValues($defaultValues);
    }

    /**
     *
     * @see HTML_QuickForm_group::accept()
     */
    public function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    /**
     * Builds the list of elements
     */
    private function build_elements()
    {
        $active_hidden_id = 'hidden_active_elements';
        $activate_button_id = 'activate_button';
        $deactivate_button_id = 'deactivate_button';
        $element_types_select_box_id = 'element_types_selector';

        $this->_elements = array();

        $this->_elements[] = new HTML_QuickForm_hidden(
            'active_hidden_' . $this->getName(), null, array('id' => $active_hidden_id)
        );

        $element_types_array = array();
        $element_types_array[- 1] =
            '-- ' . Translation::get('SelectElementType', null, Utilities::COMMON_LIBRARIES) . ' --';
        foreach ($this->element_types->get_types() as $element_type)
        {
            $element_types_array[$element_type->get_id()] = $element_type->get_name();
        }

        $this->_elements[] = new HTML_QuickForm_select(
            'element_types_' . $this->getName(), null, $element_types_array,
            array('id' => $element_types_select_box_id, 'class' => 'form-control')
        );

        $this->_elements[] = new HTML_QuickForm_text(
            'search_' . $this->getName(), null, array('class' => 'element_query form-control', 'id' => 'search_field')
        );

        $this->_elements[] = new HTML_QuickForm_stylebutton(
            'activate_' . $this->getName(), Translation::get('AddToSelection', array(), Utilities::COMMON_LIBRARIES),
            array('id' => $activate_button_id, 'class' => 'btn-primary activate_elements form-control'), '',
            new FontAwesomeGlyph('arrow-alt-circle-right', array(), null, 'fas')
        );

        $this->_elements[] = new HTML_QuickForm_stylebutton(
            'deactivate_' . $this->getName(),
            Translation::get('RemoveFromSelection', array(), Utilities::COMMON_LIBRARIES),
            array('id' => $deactivate_button_id, 'class' => 'btn-danger deactivate_elements form-control'), '',
            new FontAwesomeGlyph('arrow-alt-circle-left', array(), null, 'fas')
        );
    }

    /**
     *
     * @see HTML_QuickForm_group::exportValue()
     */
    public function exportValue($submitValues, $assoc = false)
    {
        return $this->_prepareValue($this->getValue(), $assoc);
    }

    /**
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     *
     * @param integer $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     *
     * @see HTML_QuickForm_group::getValue()
     */
    public function getValue()
    {
        $results = array();
        $values = json_decode($this->_elements[0]->getValue());

        foreach ($values as $value)
        {
            $split_by_underscores = explode('_', $value);

            $id = array_pop($split_by_underscores);
            $type = implode('_', $split_by_underscores);

            $results[$type][] = $id;
        }

        return $results;
    }

    /**
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     *
     * @param integer $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements $defaultValues
     */
    public function setDefaultValues($defaultValues)
    {
        if (!$defaultValues)
        {
            return;
        }

        $this->default_values = $defaultValues;

        $default_ids = array();
        foreach ($defaultValues->get_elements() as $default_value)
        {
            $default_ids[] = $default_value->get_id();
        }

        $encoded = json_encode($default_ids);
        $this->_elements[0]->setValue($encoded);
    }

    /**
     *
     * @return string
     */
    public function toHTML()
    {
        // Create a safe name for the id (remove array values)
        $safe_name = str_replace('[', '_', $this->getName());
        $safe_name = str_replace(']', '', $safe_name);
        $id = 'tbl_' . $safe_name;

        $html = array();

        $html[] = '<div class="element_finder" id="' . $id . '">';

        // Filter row
        $html[] = '<div class="row">';

        $html[] = '<div class="col-md-12">';

        $html[] = $this->_elements[0]->toHTML();

        $html[] = '<div class="element_finder_types">';

        $html[] = '<div class="form-group">';
        $html[] = $this->_elements[1]->toHTML();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        // Search row
        $html[] = '<div class="row">';

        $html[] = '<div class="col-md-12">';

        $html[] = '<div class="element_finder_search form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<span class="input-group-addon"><span class="fas fa-search"></span></span>';

        $this->_elements[2]->setValue('');
        $html[] = $this->_elements[2]->toHTML();

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        // Elements row
        $html[] = '<div class="row">';

        // Inactive elements
        $html[] = '<div class="col-md-12 col-lg-6">';
        $html[] = '<div class="element_finder_inactive form-group">';
        $html[] =
            '<div id="inactive_elements" class="inactive_elements form-control" style="height: ' . $this->getHeight() .
            'px; overflow: auto;"></div>';
        $html[] = '</div>';

        $html[] = '<div class="element_finder_buttons form-group">';
        $html[] = $this->_elements[3]->toHTML();
        $html[] = '</div>';

        $html[] = '</div>';

        // Active elements
        $html[] = '<div class="col-md-12 col-lg-6">';
        $html[] = '<div class="element_finder_active form-group">';
        $html[] =
            '<div id="active_elements" class="active_elements form-control" style="height: ' . $this->getHeight() .
            'px; overflow: auto;"></div>';
        $html[] = '</div>';

        $html[] = '<div class="element_finder_buttons form-group">';
        $html[] = $this->_elements[4]->toHTML();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        // Make sure everything is within the general div.
        $html[] = '</div>';

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Jquery/jquery.advelementfinder.min.js'
        );
        $html[] = '<script>';

        if ($this->default_values)
        {
            $defaultValuesText = 'defaultValues: ' . json_encode($this->default_values->as_array()) . ', ';
        }

        $configurationJson = '';

        foreach ($this->configuration as $name => $value)
        {
            $configurationJson .= ' ' . $name . ': ' . $value . ', ';
        }

        $configurationJson = substr($configurationJson, 0, strlen($configurationJson) - 2);

        $html[] = '$("#' . $id . '").advelementfinder({ name: "' . $safe_name . '", ' . $defaultValuesText .
            'elementTypes: ' . json_encode($this->element_types->as_array()) . ',' . $configurationJson . '});';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
