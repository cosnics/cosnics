<?php

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
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

    public function __construct(
        $elementName = null, $elementLabel = null, $search_url = null, $locale = array('Display' => 'Display'),
        $default = array(),
        $options = array('rescale_image' => true, 'allow_change' => false)
    )
    {
        HTML_QuickForm_group::__construct($elementName, $elementLabel);
        $this->_type = 'image_selecter';
        $this->_persistantFreeze = true;
        $this->_appendName = false;
        $this->locale = $locale;
        $this->exclude = array();
        $this->height = self::DEFAULT_HEIGHT;
        $this->width = self::DEFAULT_WIDTH;
        $this->search_url = $search_url;
        $this->options = $options;
        $this->build_elements();
        $this->setValue($default);
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
        $this->_elements = array();
        $this->_elements[] = new \HTML_QuickForm_hidden($this->getName());
        $this->_elements[] = new \HTML_QuickForm_text(
            $this->getName() . '_search',
            null,
            array('class' => 'element_query', 'id' => $this->getName() . '_search_field')
        );
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
        $calculator = new \Chamilo\Core\Repository\Quota\Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                (int) Session::get_user_id()
            )
        );

        $uploadUrl = new Redirect(
            array(
                \Chamilo\Libraries\Architecture\Application\Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::context(
                ),
                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE
            )
        );

        $dropZoneParameters = array(
            'name' => 'attachments_importer',
            'maxFilesize' => $calculator->getMaximumUploadSize(),
            'uploadUrl' => $uploadUrl->getUrl(),
            'successCallbackFunction' => 'chamilo.core.repository.importImage.processUploadedFile',
            'sendingCallbackFunction' => 'chamilo.core.repository.importImage.prepareRequest',
            'removedfileCallbackFunction' => 'chamilo.core.repository.importImage.deleteUploadedFile'
        );

        /*
         * 0 hidden 1 search
         */
        $html = array();
        $html[] = '<div id="image_select" style="display: none;">';

        $html[] = $this->addFileDropzone('attachments_importer', $dropZoneParameters, true);

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(\Chamilo\Core\Repository\Manager::context(), true) .
            'Plugin/jquery.file.upload.import.js'
        );

        if ($this->isCollapsed())
        {
            $html[] = '<button id="' . $this->getName() . '_expand_button" class="normal select">' . htmlentities(
                    $this->locale['Display']
                ) . '</button>';
        }
        else
        {
            $html[] =
                '<button id="' . $this->getName() . '_expand_button" style="display: none" class="normal select">' .
                htmlentities(
                    $this->locale['Display']
                ) . '</button>';
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
            $html[] =
                '<button id="' . $this->getName() . '_collapse_button" style="display: none" class="normal hide">' .
                htmlentities(
                    Translation::get('Hide', null, Utilities::COMMON_LIBRARIES)
                ) . '</button>';
        }
        else
        {
            $html[] = '<button id="' . $this->getName() . '_collapse_button" class="normal hide mini">' . htmlentities(
                    Translation::get('Hide', null, Utilities::COMMON_LIBRARIES)
                ) . '</button>';
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
        $is_object_set = !empty($object_id);

        $html[] = '<div id="image_container" ' . ($is_object_set ? '' : ' style="display: none;"') . '>';

        if ($is_object_set)
        {
            $image_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $object_id
            );

            $dimensions = getimagesize($image_object->get_full_path());

            $rescale_image = $this->options['rescale_image'];

            if ($rescale_image)
            {
                $scaledDimensions = Utilities::scaleDimensions(
                    600,
                    450,
                    array('width' => $dimensions[0], 'height' => $dimensions[1])
                );
            }
            else
            {
                $scaledDimensions = array('thumbnailWidth' => $dimensions[0], 'thumbnailHeight' => $dimensions[1]);
            }

            $html[] = '<div id="selected_image" style="width: ' . $scaledDimensions['thumbnailWidth'] . 'px; height: ' .
                $scaledDimensions['thumbnailHeight'] . 'px; background-size: ' . $scaledDimensions['thumbnailWidth'] .
                'px ' . $scaledDimensions['thumbnailHeight'] . 'px;background-image: url(' .
                \Chamilo\Core\Repository\Manager::get_document_downloader_url($image_object->get_id()) . ')"></div>';
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
                    Translation::get('SelectAnotherImage')
                ) . '</button>';
            $html[] = '<div class="clear">&nbsp;</div>';
        }

        $html[] = '</div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Plugin/Jquery/jquery.imageselecter.js'
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

        $load_elements = $this->options['load_elements'];
        $load_elements =
            (isset($load_elements) && $load_elements == false ? ', loadElements: false' : ', loadElements: true');

        $rescale_image = $this->options['rescale_image'];
        $rescale_image =
            (isset($rescale_image) && $rescale_image == false ? ', rescaleImage: false' : ', rescaleImage: true');

        $default_query = $this->options['default_query'];
        $default_query =
            (isset($default_query) && !empty($default_query) ? ', defaultQuery: "' . $default_query . '"' : '');

        $html[] = '$(function () {';
        $html[] = '	$(document).ready(function ()';
        $html[] = '	{';
        $html[] = '		$("#' . $id . '").elementselecter({
        	name: "' . $this->getName() . '",
        	search: "' .
            $this->search_url . '"' . $load_elements . $rescale_image . $default_query . ' });';
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

    protected function addFileDropzone($elementName, $dropzoneOptions = array(), $includeLabel = true)
    {
        $dropzoneHtml = array();

        $dropzoneHtml[] = '<div id="' . $elementName . '-upload-container">';
        $dropzoneHtml[] = '<div id="' . $elementName . '-upload-input">';
        $dropzoneHtml[] = '<input type="file" name="' . $elementName . '">';
        $dropzoneHtml[] = '</div>';

        $dropzoneHtml[] = '<div id="' . $elementName . '-upload" class="file-upload">';

        $dropzoneHtml[] = '<div class="file-previews files" id="' . $elementName . '-previews">';
        $dropzoneHtml[] = '<div id="' . $elementName . '-template" class="thumbnail pull-left">';
        $dropzoneHtml[] = '<div class="preview">';
        $dropzoneHtml[] = '<div class="file-upload-no-preview">';
        $dropzoneHtml[] = '<span class="glyphicon glyphicon-file"></span>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<img data-dz-thumbnail />';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<div class="caption">';
        $dropzoneHtml[] = '<h3 data-dz-name></h3>';
        $dropzoneHtml[] = '<strong class="error text-danger" data-dz-errormessage></strong>';
        $dropzoneHtml[] = '<p class="size" data-dz-size></p>';
        $dropzoneHtml[] = '<div>';
        $dropzoneHtml[] =
            '<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">';
        $dropzoneHtml[] =
            '<div class="progress-bar progress-bar-success" style="width: 0%;" data-dz-uploadprogress></div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<div class="file-upload-buttons">';
        $dropzoneHtml[] = '<button data-dz-remove class="btn btn-warning cancel">';
        $dropzoneHtml[] = '<i class="glyphicon glyphicon-ban-circle"></i> <span>' . $this->getTranslation('Cancel') .
            '</span>';
        $dropzoneHtml[] = '</button>';
        $dropzoneHtml[] = '<button data-dz-remove class="btn btn-danger delete">';
        $dropzoneHtml[] = '<i class="glyphicon glyphicon-trash"></i> <span>' . $this->getTranslation('Delete') .
            '</span>';
        $dropzoneHtml[] = '</button>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        $dropzoneHtml[] = '<div class="clearfix"></div>';
        $dropzoneHtml[] = '<div class="panel panel-default">';
        $dropzoneHtml[] = '<div class="panel-body">';
        $dropzoneHtml[] =
            '<span class="actions"><span class="glyphicon glyphicon-upload"></span>&nbsp;<span class="glyphicon glyphicon-plus-sign fileinput-button dz-clickable"></span></span>';

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<div class="panel-footer">';
        $dropzoneHtml[] = $this->getTranslation('DropFileHereMessage');
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        $dropzoneOptionsString = array();

        foreach ($dropzoneOptions as $optionKey => $optionValue)
        {
            $dropzoneOptionsString[] = $optionKey . ': \'' . $optionValue . '\'';
        }

        $dropzoneHtml[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Plugin/Jquery/jquery.file.upload.js'
        );

        $dropzoneHtml[] = '<script type="text/javascript">';
        $dropzoneHtml[] = '$(document).ready(function() {';
        $dropzoneHtml[] = '$("#' . $elementName . '-upload-container").fileUpload({' .
            implode(', ', $dropzoneOptionsString) . '});';
        $dropzoneHtml[] = '});';
        $dropzoneHtml[] = '</script>';

        $dropzoneHtml[] = '</div>';

        return implode(PHP_EOL, $dropzoneHtml);
    }

    /**
     * Helper Function
     *
     * @param string $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array())
    {
        return Translation::getInstance()->getTranslation($variable, $parameters, Utilities::COMMON_LIBRARIES);
    }
}
