<?php

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package common.html.formvalidator.Element
 */

/**
 * Advanced ajax based element finder.
 * Includes multiple entities, advanced filtering, multiple selects
 *
 * @author Sven Vanpoucke
 */
class HTML_QuickForm_advanced_element_finder extends HTML_QuickForm_group
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 292;
    const CONFIG_MAX_SELECTABLE_ITEMS = 'maxSelectableItems';

    /**
     * Whether the element finder is collapsed by default
     *
     * @var boolean
     */
    private $default_collapsed;

    /**
     * Height of this element
     *
     * @var int
     */
    private $height;

    /**
     * Width of the element
     *
     * @var int
     */
    private $width;

    /**
     * List of types of elements on which can be searched
     *
     * @var AdvancedElementFinderElementTypes
     */
    private $element_types;

    /**
     * List of default selected elements
     *
     * @var AdvancedElementFinderElements
     */
    private $default_values;

    /**
     * An array of configuration values for the elementfinder (eg.
     * max number of selectable items)
     *
     * @var array
     */
    private $configuration;

    public function __construct(
        $elementName = null, $elementLabel = null, $element_types = null, $default_values = null, $config = array()
    )
    {
        HTML_QuickForm_element::__construct($elementName, $elementLabel);

        $this->configuration = $config;
        $this->_type = 'advanced_element_finder';
        $this->_persistantFreeze = true;
        $this->_appendName = false;

        $this->element_types = $element_types;

        $this->height = self::DEFAULT_HEIGHT;
        $this->width = self::DEFAULT_WIDTH;

        if (!empty($element_types))
        {
            $this->build_elements();
        }

        $this->setDefaultValues($default_values);
    }

    // Setters and getters
    public function isCollapsed()
    {
        return $this->isDefaultCollapsed() && !count($this->getValue());
    }

    public function isDefaultCollapsed()
    {
        return $this->default_collapsed;
    }

    public function setDefaultCollapsed($default_collapsed)
    {
        $this->default_collapsed = $default_collapsed;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function setWidth($width)
    {
        $this->height = $width;
    }

    public function setDefaultValues($default_values)
    {
        if (!$default_values)
        {
            return;
        }

        $this->default_values = $default_values;

        $default_ids = array();
        foreach ($default_values->get_elements() as $default_value)
        {
            $default_ids[] = $default_value->get_id();
        }

        $encoded = json_encode($default_ids);
        $this->_elements[0]->setValue($encoded);
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
            'active_hidden_' . $this->getName(),
            null,
            array('id' => $active_hidden_id)
        );

        $element_types_array = array();
        $element_types_array[- 1] = '-- ' . Translation::get('SelectElementType', null, Utilities::COMMON_LIBRARIES) .
            ' --';
        foreach ($this->element_types->get_types() as $element_type)
        {
            $element_types_array[$element_type->get_id()] = $element_type->get_name();
        }

        $this->_elements[] = new HTML_QuickForm_select(
            'element_types_' . $this->getName(),
            null,
            $element_types_array,
            array('id' => $element_types_select_box_id)
        );

        $this->_elements[] = new HTML_QuickForm_text(
            'search_' . $this->getName(),
            null,
            array('class' => 'element_query', 'id' => 'search_field')
        );

        $this->_elements[] = new HTML_QuickForm_button(
            'activate_' . $this->getName(),
            '',
            array('id' => $activate_button_id, 'class' => 'activate_elements')
        );
        $this->_elements[] = new HTML_QuickForm_button(
            'deactivate_' . $this->getName(),
            '',
            array('id' => $deactivate_button_id, 'class' => 'deactivate_elements')
        );
    }

    /**
     * Returns the values of the element finder as an associative array
     *
     * @return Array
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
     * Exports the value of this element
     */
    public function exportValue($submitValues, $assoc = false)
    {
        return $this->_prepareValue($this->getValue(), $assoc);
    }

    /**
     * Renders the element
     *
     * @return String
     */
    public function toHTML()
    {
        // Create a safe name for the id (remove array values)
        $safe_name = str_replace('[', '_', $this->getName());
        $safe_name = str_replace(']', '', $safe_name);
        $id = 'tbl_' . $safe_name;

        $html = array();

        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $safe_name . '_expand_button" class="normal select">' .
                htmlentities(Translation::get('Show', null, Utilities::COMMON_LIBRARIES)) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $safe_name . '_expand_button" style="display: none" class="normal select">' .
                htmlentities(Translation::get('Show', null, Utilities::COMMON_LIBRARIES)) . '</button>';
        }

        $html[] = '<div class="element_finder" id="' . $id . '" style="margin-top: 5px;' .
            ($this->isCollapsed() ? ' display: none;' : '') . '">';

        $html[] = $this->_elements[0]->toHTML();

        $html[] = '<div class="element_finder_types">';
        $html[] = $this->_elements[1]->toHTML();
        $html[] = '</div>';

        $html[] = '<div class="element_finder_container">';

        // Search
        $html[] = '<div class="element_finder_search">';

        $this->_elements[2]->setValue('');
        $html[] = $this->_elements[2]->toHTML();

        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $safe_name . '_collapse_button" style="display: none" class="normal hide">' .
                htmlentities(Translation::get('Hide', null, Utilities::COMMON_LIBRARIES)) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $safe_name . '_collapse_button" class="normal hide mini">' .
                htmlentities(Translation::get('Hide', null, Utilities::COMMON_LIBRARIES)) . '</button>';
        }

        $html[] = '</div>';

        $html[] = '<div class="clear"></div>';

        // The elements
        $html[] = '<div class="element_finder_elements">';

        // Inactive
        $html[] = '<div class="element_finder_inactive">';
        $html[] = '<div id="inactive_elements" class="inactive_elements" style="height: ' . $this->getHeight() .
            'px; width: ' . $this->getWidth() . 'px; overflow: auto;">';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '<div class="element_finder_buttons" style="height: ' . $this->getHeight() . 'px;">';
        $html[] = '<div class="element_finder_buttons_container">';
        $html[] = $this->_elements[3]->toHTML();
        $html[] = '<br />';
        $html[] = $this->_elements[4]->toHTML();
        $html[] = '</div></div>';

        // Active
        $html[] = '<div class="element_finder_active">';
        $html[] = '<div id="active_elements" class="active_elements" style="height: ' . $this->getHeight() .
            'px; width: ' . $this->getWidth() . 'px; overflow: auto;"></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // Make sure the elements are all within the div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // Make sure everything is within the general div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
            'Plugin/Jquery/jquery.advelementfinder.js'
        );
        $html[] = '<script type="text/javascript">';

        if ($this->default_values)
        {
            $default_values_text = 'defaultValues: ' . json_encode($this->default_values->as_array()) . ', ';
        }

        $configuration_json = '';
        foreach ($this->configuration as $name => $value)
        {
            $configuration_json .= ' ' . $name . ': ' . $value . ', ';
        }
        $configuration_json = substr($configuration_json, 0, strlen($configuration_json) - 2);

        $html[] = '$("#' . $id . '").advelementfinder({ name: "' . $safe_name . '", ' . $default_values_text .
            'elementTypes: ' . json_encode($this->element_types->as_array()) . ',' . $configuration_json . '});';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Accepts this element for rendering
     */
    public function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }
}
