<?php
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
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
 * AJAX-based tree search and image selecter.
 *
 * @author Hans De Bisschop
 */
class HTML_QuickForm_image_selecter extends \HTML_QuickForm_group
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 365;

    private static $initialized;

    private $search_url;

    private $locale;

    private $default_collapsed;

    private $height;

    private $width;

    private $exclude;

    private $defaults;

    public function __construct($elementName, $elementLabel, $search_url,
        $locale = array ('Display' => 'Display'), $default = array (), $options = array('rescale_image' => true, 'allow_change'=> false))
    {
        HTML_QuickForm_group :: __construct($elementName, $elementLabel);
        $this->_type = 'image_selecter';
        $this->_persistantFreeze = true;
        $this->_appendName = false;
        $this->locale = $locale;
        $this->exclude = array();
        $this->height = self :: DEFAULT_HEIGHT;
        $this->width = self :: DEFAULT_WIDTH;
        $this->search_url = $search_url;
        $this->options = $options;
        $this->build_elements();
        $this->setValue($default);
    }

    public function isCollapsed()
    {
        return $this->isDefaultCollapsed() && ! count($this->getValue());
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
        $this->_elements = array();
        $this->_elements[] = new \HTML_QuickForm_hidden($this->getName());
        $this->_elements[] = new \HTML_QuickForm_text(
            $this->getName() . '_search',
            null,
            array('class' => 'element_query', 'id' => $this->getName() . '_search_field'));
    }

    public function getValue()
    {
        return $this->_elements[0]->getValue();
    }

    public function exportValue($submitValues, $assoc = false)
    {
        if ($assoc)
        {
            return array($this->getName() => $this->getValue());
        }
        return $this->getValue();
    }

    public function setValue($value)
    {
        $this->_elements[0]->setValue($value);
    }

    public function toHTML()
    {
        /*
         * 0 hidden 1 search
         */
        $html = array();
        $html[] = '<div id="image_select" style="display: none;">';
        $html[] = '<div id="' . $this->getName() . '_uploadify"></div>';

        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->getName() . '_expand_button" class="normal select">' . htmlentities(
                $this->locale['Display']) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->getName() . '_expand_button" style="display: none" class="normal select">' . htmlentities(
                $this->locale['Display']) . '</button>';
        }

        $id = 'tbl_' . $this->getName();

        $html[] = '<div class="element_finder" id="' . $id . '" style="margin-top: 5px;' .
             ($this->isCollapsed() ? ' display: none;' : '') . '">';

        // Search
        $html[] = '<div class="element_finder_search">';

        $this->_elements[1]->setValue('');
        $html[] = $this->_elements[1]->toHTML();

        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->getName() . '_collapse_button" style="display: none" class="normal hide">' . htmlentities(
                Translation :: get('Hide', null, Utilities :: COMMON_LIBRARIES)) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->getName() . '_collapse_button" class="normal hide mini">' . htmlentities(
                Translation :: get('Hide', null, Utilities :: COMMON_LIBRARIES)) . '</button>';
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

        // Make sure the elements are all within the div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        // Make sure everything is within the general div.
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->_elements[0]->toHTML();

        $object_id = $this->getValue();
        $is_object_set = ! empty($object_id);

        $html[] = '<div id="image_container" ' . ($is_object_set ? '' : ' style="display: none;"') . '>';

        if ($is_object_set)
        {
            $image_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $object_id);

            $dimensions = getimagesize($image_object->get_full_path());

            $rescale_image = $this->options['rescale_image'];

            if ($rescale_image)
            {
                $scaledDimensions = Utilities :: scaleDimensions(
                    600,
                    450,
                    array('width' => $dimensions[0], 'height' => $dimensions[1]));
            }
            else
            {
                $scaledDimensions = array('thumbnailWidth' => $dimensions[0], 'thumbnailHeight' => $dimensions[1]);
            }

            $html[] = '<div id="selected_image" style="width: ' . $scaledDimensions['thumbnailWidth'] . 'px; height: ' .
                 $scaledDimensions['thumbnailHeight'] . 'px; background-size: ' . $scaledDimensions['thumbnailWidth'] .
                 'px ' . $scaledDimensions['thumbnailHeight'] . 'px;background-image: url(' .
                 \Chamilo\Core\Repository\Manager :: get_document_downloader_url($image_object->get_id()) . ')"></div>';
        }
        else
        {
            $html[] = '<div id="selected_image"></div>';
        }

        $html[] = '<div class="clear"></div>';

        $allow_change = $this->options['allow_change'];
        if ($allow_change)
        {
            $html[] = '<button id="change_image" class="negative delete">' . htmlentities(
                Translation :: get('SelectAnotherImage')) . '</button>';
            $html[] = '<div class="clear">&nbsp;</div>';
        }

        $html[] = '</div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
                 'Plugin/Uploadify/jquery.uploadify.min.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Plugin/Jquery/jquery.imageselecter.js');
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

        $load_elements = $this->options['load_elements'];
        $load_elements = (isset($load_elements) && $load_elements == false ? ', loadElements: false' : ', loadElements: true');

        $rescale_image = $this->options['rescale_image'];
        $rescale_image = (isset($rescale_image) && $rescale_image == false ? ', rescaleImage: false' : ', rescaleImage: true');

        $default_query = $this->options['default_query'];
        $default_query = (isset($default_query) && ! empty($default_query) ? ', defaultQuery: "' . $default_query . '"' : '');

        $html[] = '$(function () {';
        $html[] = '	$(document).ready(function ()';
        $html[] = '	{';
        $html[] = '		$("#' . $id . '").elementselecter({
        	name: "' . $this->getName() . '",
        	search: "' . $this->search_url . '"' . $load_elements . $rescale_image . $default_query . ' });';
        $html[] = '	});';
        $html[] = '});';

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
