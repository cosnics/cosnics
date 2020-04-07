<?php

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * AJAX-based tree search and multiselect element.
 * Use at your own risk.
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Tim De Pauw
 */
class HTML_QuickForm_element_finder extends HTML_QuickForm_group
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 292;

    /**
     *
     * @var boolean
     */
    private static $initialized;

    /**
     *
     * @var string
     */
    private $search_url;

    /**
     *
     * @var string[]
     */
    private $locale;

    /**
     *
     * @var integer
     */
    private $height;

    /**
     *
     * @var integer
     */
    private $width;

    /**
     *
     * @var integer[]
     */
    private $exclude;

    /**
     *
     * @var integer[]
     */
    private $defaults;

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string $search_url
     * @param string[] $locale
     * @param integer[] $default_values
     * @param string[] $options
     */
    public function __construct(
        $elementName = null, $elementLabel = null, $search_url = null, $locale = array('Display' => 'Display'),
        $default_values = array(), $options = array()
    )
    {
        parent::__construct($elementName, $elementLabel);
        $this->_type = 'element_finder';
        $this->_persistantFreeze = true;
        $this->_appendName = false;
        $this->locale = $locale;
        $this->exclude = array();
        $this->height = self::DEFAULT_HEIGHT;
        $this->width = self::DEFAULT_WIDTH;
        $this->search_url = $search_url;
        $this->options = $options;
        $this->build_elements();
        $this->setValue($default_values, 0);
    }

    /**
     *
     * @see HTML_QuickForm_group::accept()
     */
    public function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    private function build_elements()
    {
        $active_id = 'elf_' . $this->getName() . '_active';
        $inactive_id = 'elf_' . $this->getName() . '_inactive';
        $active_hidden_id = 'elf_' . $this->getName() . '_active_hidden';
        $activate_button_id = $inactive_id . '_button';
        $deactivate_button_id = $active_id . '_button';

        $this->_elements = array();
        $this->_elements[] = new HTML_QuickForm_hidden(
            $this->getName() . '_active_hidden', null, array('id' => $active_hidden_id)
        );
        $this->_elements[] = new HTML_QuickForm_text(
            $this->getName() . '_search', null,
            array('class' => 'element_query form-control', 'id' => $this->getName() . '_search_field')
        );
        $this->_elements[] = new HTML_QuickForm_stylebutton(
            $this->getName() . '_activate', '',
            array('id' => $activate_button_id, 'disabled' => 'disabled', 'class' => 'activate_elements'), '',
            new FontAwesomeGlyph('arrow-right', array(), null, 'fas')
        );
        $this->_elements[] = new HTML_QuickForm_button(
            $this->getName() . '_deactivate', '', array(
                'id' => $deactivate_button_id,
                'disabled' => 'disabled',
                'class' => 'deactivate_elements'
            )
        );
    }

    /**
     *
     * @param integer[] $excluded_ids
     */
    public function excludeElements($excluded_ids)
    {
        $this->exclude = array_merge($this->exclude, $excluded_ids);
    }

    /**
     *
     * @see HTML_QuickForm_group::exportValue()
     */
    public function exportValue($submitValues, $assoc = false)
    {
        if ($assoc)
        {
            return array($this->getName() => $this->getValue());
        }

        return $this->getValue();
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
        $values = $this->get_active_elements();

        /**
         * Process the array values so we end up with a 2-dimensional array Keys are the selection type, values are the
         * selected objects
         */

        foreach ($values as $value)
        {
            $value = explode('_', $value['id'], 2);

            if (!isset($results[$value[0]]) || !is_array($results[$value[0]]))
            {
                $results[$value[0]] = array();
            }

            $results[$value[0]][] = $value[1];
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
        $this->height = $width;
    }

    /**
     *
     * @return mixed
     */
    public function get_active_elements()
    {
        return unserialize($this->_elements[0]->getValue());
    }

    /**
     *
     * @param integer[] $defaults
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     *
     * @see HTML_QuickForm_group::setValue()
     */
    public function setValue($value, $element_id = 0)
    {
        $serialized = serialize($value);
        $this->_elements[$element_id]->setValue($serialized);
    }

    /**
     *
     * @return string
     */
    public function toHTML()
    {
        /*
         * 0 active hidden 1 search 2 deactivate 3 activate
         */
        $html = array();

        $id = 'tbl_' . $this->getName();

        $html[] = '<div class="element_finder row" id="' . $id . '" style="margin-top: 5px;">';
        $html[] = $this->_elements[0]->toHTML();

        $html[] = '<div class="col-lg-6">';

        // Search
        $html[] = '<div class="element_finder_search form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<span class="input-group-addon"><span class="fas fa-search"></span></span>';

        $this->_elements[1]->setValue('');
        $html[] = $this->_elements[1]->toHTML();

        $html[] = '</div>';
        $html[] = '</div>';

        // Inactive
        $html[] = '<div class="element_finder_inactive form-group">';
        $html[] =
            '<div id="elf_' . $this->getName() . '_inactive" class="inactive_elements form-control" style="height: ' .
            $this->getHeight() . 'px; overflow: auto;">';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="col-lg-6">';

        // Active
        $html[] = '<div class="element_finder_buttons form-group visible-lg-block">';
        $html[] = '<div class="form-control-static"></div>';
        $html[] = '</div>';

        $html[] = '<div class="element_finder_active form-group">';
        $html[] = '<div id="elf_' . $this->getName() . '_active" class="active_elements form-control" style="height: ' .
            $this->getHeight() . 'px; overflow: auto;"></div>';
        $html[] = '</div>';

        $html[] = '</div>';

        // Make sure everything is within the general div.
        $html[] = '</div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Jquery/jquery.elementfinder.js'
        );
        $html[] = '<script>';

        $exclude_ids = array();
        if (count($this->exclude))
        {
            $exclude_ids = array();
            foreach ($this->exclude as $exclude_id)
            {
                $exclude_ids[] = "'$exclude_id'";
            }
        }

        $html[] = 'var ' . $this->getName() . '_excluded = new Array(' . implode(',', $exclude_ids) . ');';

        $load_elements = $this->locale['load_elements'];
        $load_elements =
            (isset($load_elements) && $load_elements == true ? ', loadElements: true' : ', loadElements: false');
        $default_query = $this->locale['default_query'];
        $default_query =
            (isset($default_query) && !empty($default_query) ? ', defaultQuery: "' . $default_query . '"' : '');

        $html[] =
            '$("#' . $id . '").elementfinder({ name: "' . $this->getName() . '", search: "' . $this->search_url . '"' .
            $load_elements . $default_query . ' });';
        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
