<?php

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: element_finder.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.html.formvalidator.Element
 */

/**
 * AJAX-based tree search and multiselect element.
 * Use at your own risk.
 *
 * @author Tim De Pauw
 */
class HTML_QuickForm_element_finder extends HTML_QuickForm_group
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 292;

    private static $initialized;

    private $search_url;

    private $locale;

    private $default_collapsed;

    private $height;

    private $width;

    private $exclude;

    private $defaults;

    public function __construct(
        $elementName = null, $elementLabel = null, $search_url = null, $locale = array('Display' => 'Display'),
        $default_values = array(),
        $options = array()
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

    public function isCollapsed()
    {
        return $this->isDefaultCollapsed() && !count($this->getValue());
    }

    public function isDefaultCollapsed()
    {
        return $this->default_collapsed;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function excludeElements($excluded_ids)
    {
        $this->exclude = array_merge($this->exclude, $excluded_ids);
    }

    public function setDefaultCollapsed($default_collapsed)
    {
        $this->default_collapsed = $default_collapsed;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function setWidth($width)
    {
        $this->height = $width;
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
            $this->getName() . '_active_hidden',
            null,
            array('id' => $active_hidden_id)
        );
        $this->_elements[] = new HTML_QuickForm_text(
            $this->getName() . '_search',
            null,
            array('class' => 'element_query', 'id' => $this->getName() . '_search_field')
        );
        $this->_elements[] = new HTML_QuickForm_button(
            $this->getName() . '_activate',
            '',
            array('id' => $activate_button_id, 'disabled' => 'disabled', 'class' => 'activate_elements')
        );
        $this->_elements[] = new HTML_QuickForm_button(
            $this->getName() . '_deactivate',
            '',
            array('id' => $deactivate_button_id, 'disabled' => 'disabled', 'class' => 'deactivate_elements')
        );
    }

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

    public function exportValue($submitValues, $assoc = false)
    {
        if ($assoc)
        {
            return array($this->getName() => $this->getValue());
        }

        return $this->getValue();
    }

    public function setValue($value, $element_id = 0)
    {
        $serialized = serialize($value);
        $this->_elements[$element_id]->setValue($serialized);
    }

    public function get_active_elements()
    {
        return unserialize($this->_elements[0]->getValue());
    }

    public function toHTML()
    {
        /*
         * 0 active hidden 1 search 2 deactivate 3 activate
         */
        $html = array();

        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->getName() . '_expand_button" class="normal select">' .
                htmlentities($this->locale['Display']) . '</button>';
        }
        else
        {
            $html[] =
                '<button id="' . $this->getName() . '_expand_button" style="display: none" class="normal select">' .
                htmlentities($this->locale['Display']) . '</button>';
        }

        $id = 'tbl_' . $this->getName();

        $html[] = '<div class="element_finder" id="' . $id . '" style="margin-top: 5px;' .
            ($this->isCollapsed() ? ' display: none;' : '') . '">';
        $html[] = $this->_elements[0]->toHTML();

        // Search
        $html[] = '<div class="element_finder_search">';

        $this->_elements[1]->setValue('');
        $html[] = $this->_elements[1]->toHTML();

        if ($this->isCollapsed())
        {
            $html[] =
                '<button id="' . $this->getName() . '_collapse_button" style="display: none" class="normal hide">' .
                htmlentities(Translation::get('Hide', null, Utilities::COMMON_LIBRARIES)) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->getName() . '_collapse_button" class="normal hide mini">' .
                htmlentities(Translation::get('Hide', null, Utilities::COMMON_LIBRARIES)) . '</button>';
        }

        $html[] = '</div>';

        $html[] = '<div class="clear"></div>';

        // The elements
        $html[] = '<div class="element_finder_elements">';

        // Inactive
        $html[] = '<div class="element_finder_inactive">';
        $html[] = '<div id="elf_' . $this->getName() . '_inactive" class="inactive_elements" style="height: ' .
            $this->getHeight() . 'px; width: ' . $this->getWidth() . 'px; overflow: auto;">';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // Active
        $html[] = '<div class="element_finder_active">';
        $html[] = '<div id="elf_' . $this->getName() . '_active" class="active_elements" style="height: ' .
            $this->getHeight() . 'px; width: ' . $this->getWidth() . 'px; overflow: auto;"></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // Make sure the elements are all within the div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // Make sure everything is within the general div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Plugin/Jquery/jquery.elementfinder.js'
        );
        $html[] = '<script type="text/javascript">';

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

        $html[] = '$("#' . $id . '").elementfinder({ name: "' . $this->getName() . '", search: "' . $this->search_url .
            '"' . $load_elements . $default_query . ' });';
        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }

    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    public function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }
}
