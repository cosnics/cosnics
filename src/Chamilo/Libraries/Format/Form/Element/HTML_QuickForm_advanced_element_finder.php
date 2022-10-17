<?php
namespace Chamilo\Libraries\Format\Form\Element;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_QuickForm_element;
use HTML_QuickForm_group;
use HTML_QuickForm_hidden;
use HTML_QuickForm_Renderer;
use HTML_QuickForm_select;
use HTML_QuickForm_text;

/**
 * Advanced ajax based element finder.
 * Includes multiple entities, advanced filtering, multiple selects
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author  Sven Vanpoucke
 */
class HTML_QuickForm_advanced_element_finder extends HTML_QuickForm_group
{
    public const DEFAULT_HEIGHT = 300;
    public const DEFAULT_WIDTH = 292;

    /**
     * An array of configuration values for the elementfinder (eg.
     * max number of selectable items)
     *
     * @var string[]
     */
    private array $configuration;

    private ?AdvancedElementFinderElements $defaultValues;

    /**
     * List of types of elements on which can be searched
     */
    private ?AdvancedElementFinderElementTypes $element_types;

    private int $height;

    private int $width;

    /**
     * @param string[] $config
     */
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null,
        ?AdvancedElementFinderElementTypes $elementTypes = null, ?AdvancedElementFinderElements $defaultValues = null,
        ?array $config = []
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
     * Accepts a renderer
     *
     * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
     * @param bool $required                    Whether an element is required
     * @param ?string $error                    An error message associated with an element
     */
    public function accept(HTML_QuickForm_Renderer $renderer, bool $required = false, ?string $error = null)
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

        $this->_elements = [];

        $this->_elements[] = new HTML_QuickForm_hidden(
            'active_hidden_' . $this->getName(), null, ['id' => $active_hidden_id]
        );

        $element_types_array = [];
        $element_types_array[- 1] =
            '-- ' . Translation::get('SelectElementType', null, StringUtilities::LIBRARIES) . ' --';

        foreach ($this->element_types->get_types() as $element_type)
        {
            $element_types_array[$element_type->get_id()] = $element_type->get_name();
        }

        $this->_elements[] = new HTML_QuickForm_select(
            'element_types_' . $this->getName(), null, $element_types_array,
            ['id' => $element_types_select_box_id, 'class' => 'form-control']
        );

        $safe_name = str_replace('[', '_', $this->getName());
        $safe_name = str_replace(']', '', $safe_name);

        $this->_elements[] = new HTML_QuickForm_text(
            'search_' . $this->getName(), null,
            ['class' => 'element_query form-control', 'id' => $safe_name . '_search_field']
        );

        $this->_elements[] = new HTML_QuickForm_stylebutton(
            'activate_' . $this->getName(), Translation::get('AddToSelection', [], StringUtilities::LIBRARIES),
            ['id' => $activate_button_id, 'class' => 'btn-primary activate_elements form-control'], '',
            new FontAwesomeGlyph('arrow-alt-circle-right', [], null, 'fas')
        );

        $this->_elements[] = new HTML_QuickForm_stylebutton(
            'deactivate_' . $this->getName(), Translation::get('RemoveFromSelection', [], StringUtilities::LIBRARIES),
            ['id' => $deactivate_button_id, 'class' => 'btn-danger deactivate_elements form-control'], '',
            new FontAwesomeGlyph('arrow-alt-circle-left', [], null, 'fas')
        );
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param array $submitValues array of submitted values to search
     * @param bool $assoc         whether to return the value as associative array
     */
    public function exportValue(array &$submitValues, bool $assoc = false)
    {
        return $this->_prepareValue($this->getValue(), $assoc);
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height)
    {
        $this->height = $height;
    }

    public function getValue()
    {
        $results = [];
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

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width)
    {
        $this->width = $width;
    }

    public function setDefaultValues(?AdvancedElementFinderElements $defaultValues)
    {
        if (!$defaultValues)
        {
            return;
        }

        $this->defaultValues = $defaultValues;

        $default_ids = [];

        foreach ($defaultValues->get_elements() as $default_value)
        {
            $default_ids[] = $default_value->get_id();
        }

        $encoded = json_encode($default_ids);
        $this->_elements[0]->setValue($encoded);
    }

    public function toHTML(): string
    {
        // Create a safe name for the id (remove array values)
        $safe_name = str_replace('[', '_', $this->getName());
        $safe_name = str_replace(']', '', $safe_name);
        $id = 'tbl_' . $safe_name;

        $html = [];

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

        if ($this->defaultValues)
        {
            $defaultValuesText = 'defaultValues: ' . json_encode($this->defaultValues->as_array()) . ', ';
        }
        else
        {
            $defaultValuesText = '';
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
