<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_QuickForm;
use HTML_QuickForm_RuleRegistry;

define('NO_HTML', 1);
define('STUDENT_HTML', 2);
define('TEACHER_HTML', 3);
define('STUDENT_HTML_FULLPAGE', 4);
define('TEACHER_HTML_FULLPAGE', 5);

/**
 * Objects of this class can be used to create/manipulate/validate user input.
 *
 * @package Chamilo\Libraries\Format\Form
 */
class FormValidator extends HTML_QuickForm
{
    const FORM_METHOD_GET = 'get';

    const FORM_METHOD_POST = 'post';

    const PARAM_RESET = 'reset';

    const PARAM_SUBMIT = 'submit';

    /**
     *
     * @var boolean
     */
    private $no_errors;

    /**
     *
     * @var \HTML_QuickForm_Renderer_Default
     */
    private $renderer;

    /**
     * The HTML-editors in this form
     *
     * @var string[]
     */
    private $html_editors;

    /**
     *
     * @var boolean
     */
    private $with_progress_bar;

    /**
     * Constructor
     *
     * @param string $formName Name of the form
     * @param string $method Method ('post' (default) or 'get')
     * @param string $action Action (default is $PHP_SELF)
     * @param string $target Form's target defaults to '_self'
     * @param string[] $attributes (optional)Extra attributes for <form> tag
     * @param boolean $trackSubmit (optional)Whether to track if the form was submitted by adding a special hidden field
     *        (default = true)
     */
    public function __construct(
        $formName = '', $method = 'post', $action = '', $target = '', $attributes = array(), $trackSubmit = true
    )
    {
        $attributes['onreset'] = 'resetElements()';

        parent::__construct($formName, $method, $action, $target, $attributes, $trackSubmit);

        $this->registerAdditionalElements();
        $this->registerAdditionalRules();

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Reset.js'
        )
        );

        $this->setDefaultTemplates();

        foreach ($this->_submitValues as $index => & $value)
        {
            $value = Security::remove_XSS($value);
        }
    }

    /**
     * Returns the HTML representation of this form.
     *
     * @return string
     */
    public function render()
    {
        $error = false;

        foreach ($this->_elements as $index => $element)
        {
            if (!is_null(parent::getElementError($element->getName())))
            {
                $error = true;
                break;
            }
        }

        $return_value = '';

        if ($this->no_errors)
        {
            $renderer = $this->defaultRenderer();
            $element_template = <<<EOT
	<div class="form-row">
		<div class="form-label">
			<!-- BEGIN required --><span class="form_required">*</span> <!-- END required -->{label}
		</div>
		<div class="formw">
			<!-- BEGIN error --><!-- END error -->	{element}
		</div>
	</div>

EOT;
            $renderer->setElementTemplate($element_template);
        }
        elseif ($error)
        {
            $return_value .= Display::error_message(Translation::get('FormHasErrorsPleaseComplete'), true);
        }

        $return_value .= parent::toHtml();
        // Add the div which will hold the progress bar

        if ($this->with_progress_bar)
        {
            $return_value .= '<div id="dynamic_div" style="display:block; margin-left:40%; margin-top:10px;"></div>';
        }

        return $return_value;
    }

    /**
     *
     * @param string $elementName
     * @param string[] $dropzoneOptions
     * @param boolean $includeLabel
     * @param boolean $markRequired
     *
     * @internal param string $uploadType
     */
    public function addFileDropzone(
        $elementName, $dropzoneOptions = array(), $includeLabel = true, $markRequired = false
    )
    {
        $autoProcess = true;
        if (array_key_exists('autoProcessQueue', $dropzoneOptions))
        {
            if ($dropzoneOptions['autoProcessQueue'] === false)
            {
                $dropzoneOptions['autoProcessQueue'] = 'false';
                $autoProcess = false;
            }
        }

        $this->addElement('html', '<div id="' . $elementName . '-upload-container">');

        $this->addElement('html', '<div id="' . $elementName . '-upload-input">');
        $this->addElement('file', $elementName, sprintf(Translation::get('FileName')));
        $this->addElement('html', '</div>');

        $dropzoneHtml = array();

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

        $dropzoneHtml[] =
            '<div class="file-upload-buttons btn-toolbar btn-action-toolbar btn-action-toolbar-vertical">';
        $dropzoneHtml[] = '<div class="file-upload-buttons-group btn-group btn-group-vertical">';
        $dropzoneHtml[] = '<a data-dz-remove class="btn btn-danger delete">';

        $glyph = new FontAwesomeGlyph('trash-alt', array(), $this->getTranslation('Delete'), 'fas');
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
        $plusGlyph =
            new FontAwesomeGlyph('plus-circle', array('fileinput-button', 'dz-clickable', 'fa-3x'), null, 'fas');

        $dropzoneHtml[] =
            '<span class="actions">' . $uploadGlyph->render() . '&nbsp;' . $plusGlyph->render() . '</span>';

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<div class="panel-footer">';
        $dropzoneHtml[] = Translation::get('DropFileHereMessage');
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        if ($includeLabel)
        {
            if (array_key_exists('maxFiles', $dropzoneOptions) && $dropzoneOptions['maxFiles'] == 1)
            {
                $label = 'File';
            }
            else
            {
                $label = 'Files';
            }

            $label = $this->getTranslation($label);
        }
        else
        {
            $label = '';
        }

        if ($markRequired)
        {
            $glyph = new FontAwesomeGlyph('star', array('text-danger', 'fa-xs'), null, 'fas');
            $label .= '<span class="form_required">&nbsp;' . $glyph->render() . '</span>';
        }

        $this->addElement('static', $elementName . '_static_data', $label, implode(PHP_EOL, $dropzoneHtml));
        $this->addElement('hidden', $elementName . '_upload_data');

        $dropzoneOptionsString = array();

        foreach ($dropzoneOptions as $optionKey => $optionValue)
        {
            $dropzoneOptionsString[] = $optionKey . ': \'' . $optionValue . '\'';
        }

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'Plugin/Jquery/jquery.file.upload.js'
        )
        );

        $javascriptHtml = array();

        $javascriptHtml[] = '<script type="text/javascript">';
        $javascriptHtml[] = '$(document).ready(function() {';
        $javascriptHtml[] =
            '$("#' . $elementName . '-upload-container").fileUpload({' . implode(', ', $dropzoneOptionsString) . '});';
        $javascriptHtml[] = '});';
        $javascriptHtml[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $javascriptHtml));

        $this->addElement('html', '</div>');
    }

    /**
     *
     * @param string $name
     * @param string $label
     * @param boolean $required
     */
    public function addImageUploader($name, $label, $required)
    {
        $this->addElement('html', '<div class="image-uploader" id="image-uploader-' . $name . '">');
        $this->addElement(
            'hidden', $name, null, ' id="' . $name . '" data-element="' . $name . '" class="image-uploader-data"'
        );

        $glyph = new FontAwesomeGlyph('image', array('image-uploader-preview', 'fa-10x', 'text-muted'), null, 'fas');

        $this->addElement(
            'static', null, $label, '<div class="thumbnail" data-element="' . $name . '">' . $glyph->render() . '</div>'
        );
        $this->addElement('file', $name . '-file', null, 'class="image-uploader-file" data-element="' . $name . '"');

        $this->addElement('html', '</div>');

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'ImageUploader.js'
        )
        );
    }

    /**
     *
     * @param string $type
     * @param string $name
     * @param string $label
     * @param string $message
     * @param boolean $noMargin
     */
    protected function addMessage($type, $name, $label, $message, $noMargin = false)
    {
        $html = array();

        $html[] = '<div id="' . $name . '" class="form-row row">';

        if ($noMargin)
        {
            $html[] = '<div class="col-xs-12">';
        }
        else
        {
            $html[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label">';
            $html[] = '</div>';
            $html[] = '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">';
        }

        $html[] = '<div role="alert" class="alert alert-' . $type . '">';

        if ($label)
        {
            $html[] = '<b>' . $label . '</b><br />';
        }
        $html[] = $message;

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    public function addSaveResetButtons()
    {
        $buttons = array();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Save', null, Utilities::COMMON_LIBRARIES),
            array('class' => 'positive')
        );

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES),
            array('class' => 'normal empty')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     *
     * @param string $elementName
     * @param string[] $dropzoneOptions
     * @param boolean $includeLabel
     */
    public function addSingleFileDropzone($elementName, $dropzoneOptions = array(), $includeLabel = true)
    {
        $dropzoneOptions['maxFiles'] = 1;
        $dropzoneOptions['successCallbackFunction'] = 'chamilo.libraries.single.processUploadedFile';
        $dropzoneOptions['removedfileCallbackFunction'] = 'chamilo.libraries.single.deleteUploadedFile';

        $this->addFileDropzone($elementName, $dropzoneOptions, $includeLabel, true);

        $this->disableSubmitButton();

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
            'Plugin/Jquery/jquery.file.upload.single.js'
        )
        );
    }

    /**
     * Add a datepicker element to the form A rule is added to check if the date is a valid one
     *
     * @param string $name The element name
     * @param string $label The label for the form-element
     * @param boolean $includeTimePicker
     *
     * @return \HTML_QuickForm_datepicker
     */
    public function add_datepicker($name, $label, $includeTimePicker = true)
    {
        $element = $this->addElement(
            'datepicker', $name, $label, array('form_name' => $this->getAttribute('name'), 'class' => $name),
            $includeTimePicker
        );
        $this->addRule($name, Translation::get('InvalidDate'), 'date');

        return $element;
    }

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string[] $attributes
     * @param string $legend
     */
    public function add_element_finder_with_legend($elementName, $elementLabel, $attributes, $legend = null)
    {
        $element_finder = $this->createElement(
            'user_group_finder', $elementName . '_elements', $elementLabel, $attributes['search_url'],
            $attributes['locale'], $attributes['defaults'], $attributes['options']
        );
        $element_finder->excludeElements($attributes['exclude']);
        $this->addElement($element_finder);

        if ($legend)
        {
            $this->addElement('static', null, null, $legend->as_html());
        }
    }

    /**
     * Adds javascript code to hide a certain element.
     *
     * @param string $type
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $extra
     */
    public function add_element_hider($type, $extra = null)
    {
        $html = array();
        if ($type == 'script_block')
        {
            $html[] = '<script type="text/javascript">';
            $html[] = 'function showElement(item)';
            $html[] = '{';
            $html[] = '	if (document.getElementById(item).style.display == \'block\')';
            $html[] = '  {';
            $html[] = '		document.getElementById(item).style.display = \'none\';';
            $html[] = '  }';
            $html[] = '	else';
            $html[] = '  {';
            $html[] = '		document.getElementById(item).style.display = \'block\';';
            $html[] = '		document.getElementById(item).value = \'Version comments here ...\';';
            $html[] = '	}';
            $html[] = '}';
            $html[] = '</script>';
        }
        elseif ($type == 'script_radio')
        {
            $html[] = '<script type="text/javascript">';
            $html[] = 'function showRadio(type, item)';
            $html[] = '{';
            $html[] = '	if (type == \'A\')';
            $html[] = '	{';
            $html[] = '		for (var j = item; j >= 0; j--)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'hidden\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'visible\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '		for (var j = item; j < ' . $extra->get_version_count() . '; j++)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'visible\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'hidden\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '	}';
            $html[] = '	else if (type == \'B\')';
            $html[] = '	{';
            $html[] = '		item++;';
            $html[] = '		for (var j = item; j >= 0; j--)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'visible\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'hidden\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '		for (var j = item; j <= ' . $extra->get_version_count() . '; j++)';
            $html[] = '		{';
            $html[] = '			var it = type + j;';
            $html[] = '			if (document.getElementById(it).style.visibility == \'hidden\')';
            $html[] = '			{';
            $html[] = '				document.getElementById(it).style.visibility = \'visible\';';
            $html[] = '			};';
            $html[] = '		}';
            $html[] = '	}';
            $html[] = '}';
            $html[] = '</script>';
        }
        elseif ($type == 'begin')
        {
            $html[] = '<div id="' . $extra . '" style="display: none;">';
        }
        elseif ($type == 'end')
        {
            $html[] = '</div>';
        }

        if (isset($html))
        {
            $this->addElement('html', implode(PHP_EOL, $html));
        }
    }

    /**
     * Adds an error message to the form.
     *
     * @param string $name
     * @param string $label
     * @param string $message
     * @param boolean $noMargin
     */
    function add_error_message($name, $label, $message, $noMargin = false)
    {
        return $this->addMessage('danger', $name, $label, $message, $noMargin);
    }

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     */
    public function add_forever_or_expiration_date_window($elementName, $elementLabel = 'ExpirationDate')
    {
        $choices[] = $this->createElement(
            'radio', 'forever', '', Translation::get('Forever'), 1,
            array('onclick' => 'javascript:timewindow_hide(\'forever_timewindow\')', 'id' => 'forever')
        );
        $choices[] = $this->createElement(
            'radio', 'forever', '', Translation::get('LimitedPeriod'), 0,
            array('onclick' => 'javascript:timewindow_show(\'forever_timewindow\')')
        );
        $this->addGroup($choices, null, Translation::get($elementLabel), '<br />', false);
        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="forever_timewindow">');
        $this->addElement('datepicker', $elementName, '', array('form_name' => $this->getAttribute('name')), false);
        $this->addElement('html', '</div>');
        $this->addElement(
            'html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('forever');
					if (expiration.checked)
					{
						timewindow_hide('forever_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n"
        );
    }

    /**
     *
     * @param string $elementLabel
     * @param string $elementNamePrefix
     * @param boolean $useDimensions
     */
    public function add_forever_or_timewindow(
        $elementLabel = 'PublicationPeriod', $elementNamePrefix = '', $useDimensions = false
    )
    {
        if (!$useDimensions)
        {
            $elementName = $elementNamePrefix . 'forever';
            $fromName = $elementNamePrefix . 'from_date';
            $toName = $elementNamePrefix . 'to_date';
        }
        else
        {
            $elementName = $elementNamePrefix . '[forever]';
            $fromName = $elementNamePrefix . '[from_date]';
            $toName = $elementNamePrefix . '[to_date]';
        }

        $choices[] = $this->createElement(
            'radio', $elementName, '', Translation::get('Forever'), 1,
            array('id' => 'forever', 'onclick' => 'javascript:timewindow_hide(\'forever_timewindow\')')
        );
        $choices[] = $this->createElement(
            'radio', $elementName, '', Translation::get('LimitedPeriod'), 0,
            array('id' => 'limited', 'onclick' => 'javascript:timewindow_show(\'forever_timewindow\')')
        );
        $this->addGroup($choices, null, Translation::get($elementLabel), '', false);
        $this->addElement('html', '<div style="margin-left:25px;display:block;" id="forever_timewindow">');
        $this->add_timewindow($fromName, $toName, '', '');
        $this->addElement('html', '</div>');
        $this->addElement(
            'html', "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('forever');
					if (expiration.checked)
					{
						timewindow_hide('forever_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n"
        );
    }

    /**
     * Add a HTML-editor to the form to fill in a title.
     * A trim-filter is attached to the field. A HTML-filter is
     * attached to the field (cleans HTML) A rule is attached to check for unwanted HTML
     *
     * @param string $name
     * @param string $label
     * @param boolean $required
     * @param string[] $options
     * @param string[] $attributes
     */
    public function add_html_editor($name, $label, $required = true, $options = array(), $attributes = array())
    {
        $html_editor = new FormValidatorHtmlEditor($name, $label, $required, $options, $attributes);
        $html_editor->set_form($this);
        $html_editor->add();
    }

    /**
     * Adds an error message to the form.
     *
     * @param string $name
     * @param string $label
     * @param string $message
     * @param boolean $noMargin
     */
    function add_information_message($name, $label, $message, $noMargin = false)
    {
        return $this->addMessage('info', $name, $label, $message, $noMargin);
    }

    /**
     * Add a password field to the form.
     *
     * @param string $name
     * @param string $label
     * @param boolean $required
     * @param string[] $attributes
     *
     * @return \HTML_QuickForm_password
     */
    public function add_password($name, $label, $required = true, $attributes = array())
    {
        $element = $this->create_password($name, $label, $attributes);
        $this->addElement($element);
        if ($required)
        {
            $this->addRule(
                $name, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
            );
        }

        return $element;
    }

    /**
     * Adds a progress bar to the form.
     * Once the user submits the form, a progress bar (animated gif) is displayed. The
     * progress bar will disappear once the page has been reloaded.
     *
     * @param integer $delay
     */
    public function add_progress_bar($delay = 2)
    {
        $this->with_progress_bar = true;
        $glyph = new FontAwesomeGlyph('fa-spinner', array('fa-spin'), null, 'fas');

        $this->updateAttributes(
            "onsubmit=\"javascript: myUpload.start('dynamic_div','" . $glyph->render() . "','" .
            Translation::get('PleaseStandBy') . "','" . $this->getAttribute('id') . "');\""
        );
        $this->addElement(
            'html', '<script src="' . Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
            'Upload.js" type="text/javascript"></script>'
        );
        $this->addElement(
            'html',
            '<script type="text/javascript">var myUpload = new upload(' . (abs(intval($delay)) * 1000) . ')</script>'
        );
    }

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string[] $attributes
     * @param string $noSelection
     * @param string $legend
     */
    public function add_receivers($elementName, $elementLabel, $attributes, $noSelection = 'Everybody', $legend = null)
    {
        $choices = array();
        $choices[] = $this->createElement(
            'radio', $elementName . '_option', '', Translation::get($noSelection), '0', array(
                'onclick' => 'javascript:receivers_hide(\'receivers_window_' . $elementName . '\')',
                'id' => 'receiver_' . $elementName
            )
        );
        $choices[] = $this->createElement(
            'radio', $elementName . '_option', '', Translation::get('SelectGroupsUsers'), '1',
            array('onclick' => 'javascript:receivers_show(\'receivers_window_' . $elementName . '\')')
        );
        $this->addGroup($choices, null, $elementLabel, '', false);
        $this->addElement(
            'html', '<div style="margin-left: 25px; display: block;" id="receivers_window_' . $elementName . '">'
        );

        $this->add_element_finder_with_legend($elementName, null, $attributes, $legend);

        $this->addElement('html', '</div>');
        $this->addElement(
            'html', ">
					/* <![CDATA[ */
					var expiration_;" . $elementName . " = document.getElementById('receiver_" . $elementName . "');
					if (expiration_" . $elementName . ".checked)
					{
						receivers_hide('receivers_window_" . $elementName . "');
					}
					function receivers_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function receivers_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					function reset_receivers_" . $elementName . "();
					{
						setTimeout(
							function()
							{
								if (expiration_" . $elementName . ".checked)
									receivers_hide('receivers_window_" . $elementName . "');
								else
									receivers_show('receivers_window_" . $elementName . "');
							},30);
    				}
    				$(document).ready(function ()
					{
						$(document).on('click', ':reset', reset_receivers_" . $elementName . ");
					});
					/* ]]> */
					</script>\n"
        );
    }

    /**
     *
     * @param string $elementName
     * @param string $elementLabel
     * @param string[] $attributes
     * @param string[] $radioArray
     */
    public function add_receivers_variable($elementName, $elementLabel, $attributes, $radioArray)
    {
        $choices = array();

        if (!is_array($radioArray))
        {
            $radioArray = array($radioArray);
        }

        foreach ($radioArray as $radioType)
        {
            $choices[] = $this->createElement(
                'radio', $elementName . '_option', '', Translation::get($radioType), $radioType, array(
                    'onclick' => 'javascript:receivers_hide(\'' . $elementName . 'receivers_window\')',
                    'id' => $elementName . 'receiver'
                )
            );
        }

        $choices[] = $this->createElement(
            'radio', $elementName . '_option', '', Translation::get('SelectGroupsUsers'), '1', array(
                'onclick' => 'javascript:receivers_show(\'' . $elementName . 'receivers_window\')',
                'id' => $elementName . 'group'
            )
        );
        $this->addGroup($choices, null, $elementLabel, '<br />', false);
        $idGroup = $elementName . 'group';
        $nameWindow = $elementName . 'receivers_window';
        $this->addElement(
            'html', '<div style="margin-left: 25px; display: block;" id="' . $elementName . 'receivers_window">'
        );

        $element_finder = $this->createElement(
            'user_group_finder', $elementName . '_elements', '', $attributes['search_url'], $attributes['locale'],
            $attributes['defaults'], $attributes['options']
        );

        $element_finder->excludeElements($attributes['exclude']);

        $this->addElement($element_finder);
        $this->addElement('html', '</div>');

        $this->addElement(
            'html', ">
					/* <![CDATA[ */
					var expiration_;" . $elementName . " = document.getElementById('$idGroup');
                if (expiration_" . $elementName . ".checked)
                {
                receivers_show('$nameWindow');
    }
                else
                {
                receivers_hide('$nameWindow');
    }
                function receivers_show(item) {
                el = document.getElementById(item);
                el.style.display='';
    }
                function receivers_hide(item) {
                el = document.getElementById(item);
                el.style.display='none';
    }
                function reset_receivers_" . $elementName . "();
					{
						setTimeout(
							function()
							{
								if (expiration_" . $elementName . ".checked)
                receivers_show('$nameWindow');
                else
                receivers_hide('$nameWindow');
    },30);
    }
                $(document).ready(function ()
                {
                $(document).on('click', ':reset', reset_receivers_" . $elementName . ");
					});
					/* ]]> */
					</script>\n"
        );
    }

    /**
     * Adds a select control to the form.
     *
     * @param string $name The element name.
     * @param string $label The element label.
     * @param string[] $values Associative array of possible values.
     * @param boolean $required <code>true</code> if required (default), <code>false</code> otherwise.
     * @param string[] $attributes Element attributes (optional).
     *
     * @return \HTML_QuickForm_select The element.
     */
    public function add_select($name, $label, $values, $required = true, $attributes = array())
    {
        $element = $this->addElement('select', $name, $label, $values, $attributes);
        if ($required)
        {
            $this->addRule(
                $name, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
            );
        }

        return $element;
    }

    /**
     * Add a textfield to the form.
     * A trim-filter is attached to the field.
     *
     * @param string $name The element name
     * @param string $label The label for the form-element
     * @param boolean $required Is the form-element required (default=true)
     * @param string[] $attributes Optional list of attributes for the form-element
     *
     * @return \HTML_QuickForm_input The element.
     * @throws \Exception
     */
    public function add_textfield($name, $label, $required = true, $attributes = array())
    {
        if (!array_key_exists('class', $attributes))
        {
            $attributes['class'] = 'form-control';
        }

        $element = $this->addElement('text', $name, $label, $attributes);
        $this->applyFilter($name, 'trim');
        if ($required)
        {
            $this->addRule(
                $name, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
            );
        }

        return $element;
    }

    /**
     * Add a timepicker element to the form
     *
     * @param string $name The element name
     * @param string $label The label for the form-element
     * @param boolean $includeMinutesPicker
     *
     * @return \HTML_QuickForm_timepicker The element.
     */
    public function add_timepicker($name, $label, $includeMinutesPicker = true)
    {
        $element = $this->addElement(
            'timepicker', $name, $label, array('form_name' => $this->getAttribute('name'), 'class' => $name),
            $includeMinutesPicker
        );

        return $element;
    }

    /**
     * Add a timewindow element to the form.
     * 2 datepicker elements are added and a rule to check if the first date is
     * before the second one.
     *
     * @param string $firstName The element name
     * @param string $secondName The element name
     * @param string $firstLabel The label for the form-element
     * @param string $secondLabel The label for the form-element
     * @param boolean $include_time_picker
     *
     * @return \HTML_QuickForm_datepicker[]
     */
    public function add_timewindow($firstName, $secondName, $firstLabel, $secondLabel, $includeTimePicker = true)
    {
        $elements = array();

        $elements[] = $this->add_datepicker($firstName, $firstLabel, $includeTimePicker);
        $elements[] = $this->add_datepicker($secondName, $secondLabel, $includeTimePicker);

        $this->addRule(
            array($firstName, $secondName), Translation::get('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte'
        );

        return $elements;
    }

    /**
     * Adds a warning message to the form.
     *
     * @param string $name
     * @param string $label
     * @param string $message
     * @param boolean $noMargin
     */
    function add_warning_message($name, $label, $message, $noMargin = false)
    {
        return $this->addMessage('warning', $name, $label, $message, $noMargin);
    }

    /**
     *
     * @param \HTML_QuickForm_element $elements
     * @param string $name
     * @param string $groupLabel
     * @param string $separator
     * @param string $appendName
     *
     * @return \HTML_QuickForm_group
     */
    function &createGroup($elements, $name = null, $groupLabel = '', $separator = null, $appendName = true)
    {
        static $anonGroups = 1;

        if (0 == strlen($name))
        {
            $name = 'qf_group_' . $anonGroups ++;
            $appendName = false;
        }

        return $this->createElement('group', $name, $groupLabel, $elements, $separator, $appendName);
    }

    /**
     *
     * @param string $name
     * @param string $label
     * @param string[] $options
     * @param string[] $attributes
     *
     * @return \HTML_QuickForm_textarea
     */
    public function create_html_editor($name, $label, $options = array(), $attributes = array())
    {
        $html_editor = new FormValidatorHtmlEditor($name, $label, false, $options, $attributes);
        $html_editor->set_form($this);

        return $html_editor->create();
    }

    /**
     * Create a password field.
     *
     * @param string $name
     * @param string $label
     * @param string[] $attributes
     *
     * @return \HTML_QuickForm_password
     */
    public function create_password($name, $label, $attributes = array())
    {
        if (!array_key_exists('size', $attributes))
        {
            $attributes['size'] = 50;
        }

        if (!array_key_exists('class', $attributes))
        {
            $attributes['class'] = 'form-control';
        }

        $element = $this->createElement('password', $name, $label, $attributes);

        return $element;
    }

    /**
     *
     * @param string $name
     * @param string $label
     * @param string[] $attributes
     *
     * @return \HTML_QuickForm_text
     */
    public function create_textfield($name, $label, $attributes = array())
    {
        // if (! array_key_exists('size', $attributes))
        // {
        // $attributes['size'] = 50;
        // }
        if (!array_key_exists('class', $attributes))
        {
            $attributes['class'] = 'form-control';
        }

        $element = $this->createElement('text', $name, $label, $attributes);

        return $element;
    }

    /**
     * Disables the submit button
     */
    protected function disableSubmitButton()
    {
        $javascriptHtml = array();

        $javascriptHtml[] = '<script type="text/javascript">';
        $javascriptHtml[] = '$(document).ready(function() {';
        $javascriptHtml[] = '$(\'button[type=submit]\').prop(\'disabled\', true)';
        $javascriptHtml[] = '});';
        $javascriptHtml[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $javascriptHtml));
    }

    /**
     *
     * @param string $extraClasses
     *
     * @return string
     */
    public function getElementTemplate($extraClasses = null)
    {
        $element_template = array();
        $glyph = new FontAwesomeGlyph('star', array('text-danger', 'fa-xs'), null, 'fas');

        $element_template[] = '<div class="form-row row ' . $extraClasses . '">';
        $element_template[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label control-label">';
        $element_template[] = '{label}<!-- BEGIN required --><span class="form_required">&nbsp;' . $glyph->render() .
            '</span> <!-- END required -->';
        $element_template[] = '</div>';
        $element_template[] = '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">';
        $element_template[] =
            '<div class="element"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}</div>';
        $element_template[] = '<div class="form_feedback"></div></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';

        return implode(PHP_EOL, $element_template);
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
     * @param \HTML_QuickForm_group $group
     * @param string $elementName
     *
     * @return \HTML_QuickForm_element
     */
    public function get_group_element($groupName, $elementName)
    {
        $groupElements = $this->getElement($groupName)->getElements();

        foreach ($groupElements as $groupElement)
        {
            if ($groupElement->getName() == $elementName)
            {
                return $groupElement;
            }
        }
    }

    /**
     *
     * @return string[]
     */
    public function get_html_editors()
    {
        return $this->html_editors;
    }

    /**
     * Returns the renderer
     *
     * @return \HTML_QuickForm_Renderer_Default
     */
    public function get_renderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \HTML_QuickForm_Renderer_Default $renderer
     */
    public function set_renderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Formats an multiple dimension array to a single dimension array to support default values in the quickform
     * library because quickform produces arrays when an array is used in the name, but quickform does not accept arrays
     * for the default values, instead the inner arrays are converted as strings
     *
     * @param string[][] $array
     * @param integer $level
     *
     * @return string[]
     */
    protected function multi_dimensional_array_to_single_dimensional_array($array, $level = 0)
    {
        $single_dimension_array = array();

        foreach ($array as $key => $element)
        {
            $key = ($level == 0) ? $key : '[' . $key . ']';

            if (is_array($element))
            {
                $single_array = $this->multi_dimensional_array_to_single_dimensional_array($element, $level + 1);
                foreach ($single_array as $child_key => $child_element)
                {
                    $single_dimension_array[$key . $child_key] = $child_element;
                }
            }
            else
            {
                $element_string = $element;
                $single_dimension_array[$key] = $element_string;
            }
        }

        return $single_dimension_array;
    }

    /**
     *
     * @param integer $value
     *
     * @return integer
     */
    public function parse_checkbox_value($value = null)
    {
        if (isset($value) && $value == 1)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function registerAdditionalElements()
    {
        $dir = __DIR__ . '/';

        // Date and timepicker elements
        $this->registerElementType(
            'datepicker', $dir . 'Element/HTML_QuickForm_datepicker.php', 'HTML_QuickForm_datepicker'
        );
        $this->registerElementType(
            'timepicker', $dir . 'Element/HTML_QuickForm_timepicker.php', 'HTML_QuickForm_timepicker'
        );

        // Element finder elements
        $this->registerElementType(
            'upload_or_create', $dir . 'Element/HTML_QuickForm_upload_or_create.php', 'HTML_QuickForm_upload_or_create'
        );
        $this->registerElementType(
            'element_finder', $dir . 'Element/HTML_QuickForm_element_finder.php', 'HTML_QuickForm_element_finder'
        );
        $this->registerElementType(
            'advanced_element_finder', $dir . 'Element/HTML_QuickForm_advanced_element_finder.php',
            'HTML_QuickForm_advanced_element_finder'
        );
        $this->registerElementType(
            'image_selecter', $dir . 'Element/HTML_QuickForm_image_selecter.php', 'HTML_QuickForm_image_selecter'
        );

        $this->registerElementType(
            'user_group_finder', $dir . 'Element/HTML_QuickForm_user_group_finder.php',
            'HTML_QuickForm_user_group_finder'
        );

        // Button elements
        $this->registerElementType(
            'style_button', $dir . 'Element/HTML_QuickForm_stylebutton.php', 'HTML_QuickForm_stylebutton'
        );
        $this->registerElementType(
            'style_submit_button', $dir . 'Element/HTML_QuickForm_stylesubmitbutton.php',
            'HTML_QuickForm_stylesubmitbutton'
        );
        $this->registerElementType(
            'style_reset_button', $dir . 'Element/HTML_QuickForm_styleresetbutton.php',
            'HTML_QuickForm_styleresetbutton'
        );

        // Replacing some default elements
        $this->registerElementType(
            'radio', $dir . 'Element/HTML_QuickForm_bootstrap_radio.php', 'HTML_QuickForm_bootstrap_radio'
        );

        $this->registerElementType(
            'checkbox', $dir . 'Element/HTML_QuickForm_extended_checkbox.php', 'HTML_QuickForm_extended_checkbox'
        );

        $this->registerElementType(
            'file', $dir . 'Element/HTML_QuickForm_stylefile.php', 'HTML_QuickForm_stylefile'
        );

        $this->registerElementType('toggle', $dir . 'Element/HTML_QuickForm_toggle.php', 'HTML_QuickForm_toggle');

        $this->registerElementType('category', $dir . 'Element/HTML_QuickForm_category.php', 'HTML_QuickForm_category');
    }

    public function registerAdditionalRules()
    {
        $dir = __DIR__ . '/';

        $this->registerRule('date', null, 'HTML_QuickForm_Rule_Date', $dir . 'Rule/HTML_QuickForm_Rule_Date.php');
        $this->registerRule(
            'date_compare', null, 'HTML_QuickForm_Rule_DateCompare', $dir . 'Rule/HTML_QuickForm_Rule_DateCompare.php'
        );
        $this->registerRule(
            'number_compare', null, 'HTML_QuickForm_Rule_NumberCompare',
            $dir . 'Rule/HTML_QuickForm_Rule_NumberCompare.php'
        );
        $this->registerRule(
            'username_available', null, 'HTML_QuickForm_Rule_UsernameAvailable',
            $dir . 'Rule/HTML_QuickForm_Rule_UsernameAvailable.php'
        );
        $this->registerRule(
            'username', null, 'HTML_QuickForm_Rule_Username', $dir . 'Rule/HTML_QuickForm_Rule_Username.php'
        );
        $this->registerRule(
            'filetype', null, 'HTML_QuickForm_Rule_Filetype', $dir . 'Rule/HTML_QuickForm_Rule_Filetype.php'
        );

        $this->registerRule(
            'disk_quota', null, 'HTML_QuickForm_Rule_DiskQuota', $dir . 'Rule/HTML_QuickForm_Rule_DiskQuota.php'
        );
    }

    /**
     *
     * @param string $name
     */
    public function register_html_editor($name)
    {
        $this->html_editors[] = $name;
    }

    public function setDefaultTemplates()
    {
        $this->renderer = $this->defaultRenderer();

        $form_template = <<<EOT
<form {attributes}>
{content}
	<div class="clear">
		&nbsp;
	</div>
</form>
EOT;
        $this->renderer->setFormTemplate($form_template);

        $this->renderer->setElementTemplate($this->getElementTemplate());

        $header_template = array();
        $header_template[] = '<div class="form-row">';
        $header_template[] = '<div class="form_header">{header}</div>';
        $header_template[] = '</div>';
        $header_template = implode(PHP_EOL, $header_template);

        $this->renderer->setHeaderTemplate($header_template);

        $glyph = new FontAwesomeGlyph('star', array('text-danger', 'fa-xs'), null, 'fas');

        HTML_QuickForm::setRequiredNote(
            '<span class="form_required">&nbsp;' . $glyph->render() . '&nbsp;<small>' .
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES) . '</small></span>'
        );
        $required_note_template = <<<EOT
	<div class="form-row row">
		<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label"></div>
		<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">{requiredNote}</div>
	</div>
EOT;
        $this->renderer->setRequiredNoteTemplate($required_note_template);
    }

    /**
     *
     * @param string $elementName
     */
    public function setInlineElementTemplate($elementName)
    {
        $this->get_renderer()->setElementTemplate($this->getElementTemplate('form-inline'), $elementName);
    }

    public function set_error_reporting($enabled)
    {
        $this->no_errors = !$enabled;
    }

    /**
     * Returns the HTML representation of this form.
     *
     * @return string
     * @deprecated Use render() now
     */
    public function toHtml()
    {
        return $this->render();
    }

    /**
     *
     * @param string $name
     */
    public function unregister_html_editor($name)
    {
        $key = array_search($name, $this->html_editors);

        if ($key)
        {
            unset($this->html_editors[$key]);
        }
    }

    /**
     *
     * @param string[] $value
     *
     * @return boolean
     */
    public function validate_csv($value)
    {
        $registry = &HTML_QuickForm_RuleRegistry::singleton();
        $rulenr = '-1';

        foreach ($this->_rules as $target => $rules)
        {
            $rulenr ++;
            $submitValue = $value[$rulenr];

            foreach ($rules as $elementName => $rule)
            {
                $result = $registry->validate($rule['type'], $submitValue, $rule['format'], false);
                if (!$this->isElementRequired($target))
                {
                    if (!isset($submitValue) || '' == $submitValue)
                    {
                        continue 2;
                    }
                }

                if (!$result || (!empty($rule['howmany']) && $rule['howmany'] > (int) $result))
                {

                    if (isset($rule['group']))
                    {

                        $this->_errors[$rule['group']] = $rule['message'];
                    }
                    else
                    {
                        $this->_errors[$target] = $rule['message'];
                    }
                }
            }
        }

        return (0 == count($this->_errors));
    }
}
