<?php

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * AJAX-based tree search and image selecter.
 *
 * @package Chamilo\Libraries\Format\Form\Element
 * @author Hans De Bisschop
 */
class HTML_QuickForm_image_selecter extends HTML_QuickForm_group
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 365;

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
     * @param integer[] $default
     * @param string[] $options
     */
    public function __construct(
        $elementName = null, $elementLabel = null, $search_url = null, $locale = array('Display' => 'Display'),
        $default = array(), $options = array('rescale_image' => true, 'allow_change' => false)
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

    /**
     *
     * @see HTML_QuickForm_group::accept()
     */
    public function accept($renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    /**
     *
     * @param string $elementName
     * @param string[] $dropzoneOptions
     * @param boolean $includeLabel
     *
     * @return string
     */
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

        $glyph = new FontAwesomeGlyph('file', array(), null, 'fas');
        $dropzoneHtml[] = $glyph->render();

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

        $dropzoneHtml[] = '<div class="file-upload-buttons btn-toolbar btn-action-toolbar btn-action-toolbar-vertical">';
        $dropzoneHtml[] = '<div class="file-upload-buttons-group btn-group btn-group-vertical">';
        $dropzoneHtml[] = '<a data-dz-remove class="btn btn-warning cancel">';

        $glyph = new FontAwesomeGlyph('ban', array(), null, 'fas');
        $dropzoneHtml[] = $glyph->render() . ' <span>' . $this->getTranslation('Cancel') . '</span>';

        $dropzoneHtml[] = '</a>';
        $dropzoneHtml[] = '<a data-dz-remove class="btn btn-danger delete">';

        $glyph = new FontAwesomeGlyph('trash-alt', array(), null, 'fas');
        $dropzoneHtml[] = $glyph->render() . ' <span>' . $this->getTranslation('Delete') . '</span>';

        $dropzoneHtml[] = '</a>';

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        $dropzoneHtml[] = '<div class="clearfix"></div>';
        $dropzoneHtml[] = '<div class="panel panel-default">';
        $dropzoneHtml[] = '<div class="panel-body">';

        $uploadGlyph = new FontAwesomeGlyph('upload', array('fa-3x'), null, 'fas');
        $plusGlyph = new FontAwesomeGlyph(
            'plus-circle', array('fileinput-button', 'dz-clickable', 'fa-3x'), null, 'fas'
        );
        $dropzoneHtml[] =
            '<span class="actions">' . $uploadGlyph->render() . '&nbsp;' . $plusGlyph->render() . '</span>';

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

        $dropzoneHtml[] = '<script>';
        $dropzoneHtml[] = '$(document).ready(function() {';
        $dropzoneHtml[] =
            '$("#' . $elementName . '-upload-container").fileUpload({' . implode(', ', $dropzoneOptionsString) . '});';
        $dropzoneHtml[] = '});';
        $dropzoneHtml[] = '</script>';

        $dropzoneHtml[] = '</div>';

        return implode(PHP_EOL, $dropzoneHtml);
    }

    private function build_elements()
    {
        $this->_elements = array();
        $this->_elements[] = new HTML_QuickForm_hidden($this->getName());
        $this->_elements[] = new HTML_QuickForm_text(
            $this->getName() . '_search', null,
            array('class' => 'element_query form-control', 'id' => $this->getName() . '_search_field')
        );
    }

    /**
     *
     * @param integer[] $excludedIds
     */
    public function excludeElements($excludedIds)
    {
        $this->exclude = array_merge($this->exclude, $excludedIds);
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
     * Helper Function
     *
     * @param string $variable
     * @param string[] $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array())
    {
        return Translation::getInstance()->getTranslation($variable, $parameters, Utilities::COMMON_LIBRARIES);
    }

    /**
     *
     * @see HTML_QuickForm_group::getValue()
     */
    public function getValue()
    {
        return $this->_elements[0]->getValue();
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
    public function setValue($value)
    {
        $this->_elements[0]->setValue($value);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function toHTML()
    {
        $calculator = new \Chamilo\Core\Repository\Quota\Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class_name(), (int) Session::get_user_id()
            )
        );

        $uploadUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::context(),
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
            Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'Plugin/jquery.file.upload.import.js'
        );

        $id = 'tbl_' . $this->getName();

        $html[] = '<div class="element_finder row" id="' . $id . '">';

        $html[] = '<div class="col-sm-12">';

        // Search
        $html[] = '<div class="element_finder_search form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<span class="input-group-addon"><span class="fas fa-search"></span></span>';

        $this->_elements[1]->setValue('');
        $html[] = $this->_elements[1]->toHTML();

        $html[] = '</div>';
        $html[] = '</div>';

        // The elements

        // Inactive
        $html[] = '<div class="element_finder_inactive form-group">';
        $html[] =
            '<div id="elf_' . $this->getName() . '_inactive" class="inactive_elements form-control" style="height: ' .
            $this->getHeight() . 'px; overflow: auto;">';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->_elements[0]->toHTML();

        $object_id = $this->getValue();
        $is_object_set = !empty($object_id);

        $html[] = '<div id="image_container" ' . ($is_object_set ? '' : ' style="display: none;"') . '>';

        if ($is_object_set)
        {
            $image_object = DataManager::retrieve_by_id(
                ContentObject::class_name(), $object_id
            );

            $dimensions = getimagesize($image_object->get_full_path());

            $rescale_image = $this->options['rescale_image'];

            if ($rescale_image)
            {
                $scaledDimensions = Utilities::scaleDimensions(
                    600, 450, array('width' => $dimensions[0], 'height' => $dimensions[1])
                );
            }
            else
            {
                $scaledDimensions = array('thumbnailWidth' => $dimensions[0], 'thumbnailHeight' => $dimensions[1]);
            }

            $html[] = '<div id="selected_image" style="width: ' . $scaledDimensions['thumbnailWidth'] . 'px; height: ' .
                $scaledDimensions['thumbnailHeight'] . 'px; background-size: ' . $scaledDimensions['thumbnailWidth'] .
                'px ' . $scaledDimensions['thumbnailHeight'] . 'px;background-image: url(' .
                Manager::get_document_downloader_url($image_object->get_id()) . ')"></div>';
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
        	search: "' . $this->search_url . '"' . $load_elements . $rescale_image . $default_query . ' });';
        $html[] = '	});';
        $html[] = '});';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
