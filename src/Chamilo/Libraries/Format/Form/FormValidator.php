<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_advanced_element_finder;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_bootstrap_radio;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_category;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_datepicker;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_extended_checkbox;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_stylebutton;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_stylefile;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_styleresetbutton;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_stylesubmitbutton;
use Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_toggle;
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_Date;
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_DateCompare;
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_Filetype;
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_NumberCompare;
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_Username;
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_UsernameAvailable;
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_ValidateDatabaseConnection;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Form\FormTabsGenerator;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_QuickForm;

/**
 * Objects of this class can be used to create/manipulate/validate user input.
 *
 * @package Chamilo\Libraries\Format\Form
 */
class FormValidator extends HTML_QuickForm
{
    use DependencyInjectionContainerTrait;

    public const FORM_METHOD_GET = 'get';
    public const FORM_METHOD_POST = 'post';

    public const PARAM_RESET = 'reset';
    public const PARAM_SUBMIT = 'submit';

    public const PROPERTY_HTML_EDITORS = 'html_editors';
    public const PROPERTY_TIME_PERIOD_FOREVER = 'forever';
    public const PROPERTY_TIME_PERIOD_FROM_DATE = 'from_date';
    public const PROPERTY_TIME_PERIOD_TO_DATE = 'to_date';

    /**
     * The HTML-editors in this form
     *
     * @var string[]
     */
    private $html_editors;

    /**
     * @var bool
     */
    private $no_errors;

    /**
     * @var \HTML_QuickForm_Renderer_Default
     */
    private $renderer;

    /**
     * Constructor
     *
     * @param string $formName     Name of the form
     * @param string $method       Method (FormValidator::FORM_METHOD_POST (default) or FormValidator::FORM_METHOD_GET)
     * @param string $action       Action (default is $PHP_SELF)
     * @param string $target       Form's target defaults to '_self'
     * @param string[] $attributes (optional)Extra attributes for <form> tag
     * @param bool $trackSubmit    (optional)Whether to track if the form was submitted by adding a special hidden field
     *                             (default = true)
     */
    public function __construct(
        $formName = '', $method = self::FORM_METHOD_POST, $action = '', $target = '', $attributes = [],
        $trackSubmit = true
    )
    {
        $attributes['onreset'] = 'resetElements()';

        parent::__construct($formName, $method, $action, $target, $attributes, $trackSubmit);

        $this->registerAdditionalElements();
        $this->registerAdditionalRules();

        $this->initializeContainer();

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'Reset.js'
        )
        );

        foreach ($this->_submitValues as $index => & $value)
        {
            $value = $this->getSecurity()->removeXSS($value);
        }

        $this->setDefaultTemplates();
    }

    /**
     * Returns the HTML representation of this form.
     *
     * @return string
     * @throws \QuickformException
     */
    public function render(?string $in_data = null): string
    {
        $error = false;

        foreach ($this->_elements as $index => $element)
        {
            if ($element->getName() && !is_null(parent::getElementError($element->getName())))
            {
                $error = true;
                break;
            }
        }

        $html = [];

        if ($this->no_errors)
        {
            $renderer = $this->defaultRenderer();
            $element_template = <<<EOT
	<div class="form-row">
		<div class="form-label">
			<!-- BEGIN required --><span class="text-danger">*</span> <!-- END required -->{label}
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

            $html[] = Display::error_message($this->getTranslation('FormHasErrorsPleaseComplete'));
        }

        $html[] = parent::toHtml($in_data);

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string $elementName
     * @param string[] $dropzoneOptions
     * @param bool $includeLabel
     * @param bool $markRequired
     *
     * @internal param string $uploadType
     */
    public function addFileDropzone(
        $elementName, $dropzoneOptions = [], $includeLabel = true, $markRequired = false
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
        $this->addElement('file', $elementName, sprintf($this->getTranslation('FileName')));
        $this->addElement('html', '</div>');

        $dropzoneHtml = [];

        $dropzoneHtml[] = '<div id="' . $elementName . '-upload" class="file-upload">';

        $dropzoneHtml[] = '<div class="file-previews files" id="' . $elementName . '-previews">';
        $dropzoneHtml[] = '<div id="' . $elementName . '-template" class="thumbnail pull-left">';
        $dropzoneHtml[] = '<div class="preview">';
        $dropzoneHtml[] = '<div class="file-upload-no-preview">';

        $glyph = new FontAwesomeGlyph('file', [], null, 'fas');
        $dropzoneHtml[] = $glyph->render();

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<img data-dz-thumbnail />';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<div class="caption">';
        $dropzoneHtml[] = '<h3 data-dz-name>Dropzone Name</h3>';
        $dropzoneHtml[] = '<strong class="error text-danger" data-dz-errormessage></strong>';
        $dropzoneHtml[] = '<p class="size" data-dz-size></p>';
        $dropzoneHtml[] = '<div>';
        $dropzoneHtml[] =
            '<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">';
        $dropzoneHtml[] =
            '<div class="progress-bar progress-bar-success" style="width: 0;" data-dz-uploadprogress></div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        $dropzoneHtml[] =
            '<div class="file-upload-buttons btn-toolbar btn-action-toolbar btn-action-toolbar-vertical">';
        $dropzoneHtml[] = '<div class="file-upload-buttons-group btn-group btn-group-vertical">';
        $dropzoneHtml[] = '<a data-dz-remove class="btn btn-danger delete">';

        $glyph = new FontAwesomeGlyph('trash-alt', [], $this->getTranslation('Delete'), 'fas');
        $dropzoneHtml[] = $glyph->render() . ' <span>' . $this->getTranslation('Delete') . '</span>';

        $dropzoneHtml[] = '</a>';

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '</div>';

        $dropzoneHtml[] = '<div class="clearfix"></div>';
        $dropzoneHtml[] = '<div class="panel panel-default">';
        $dropzoneHtml[] = '<div class="panel-body text-center" role="button">';

        $uploadGlyph = new FontAwesomeGlyph('upload', ['fa-3x', 'text-primary'], null, 'fas');
        $plusGlyph = new FontAwesomeGlyph(
            'plus-circle', ['fileinput-button', 'dz-clickable', 'fa-3x', 'text-primary'], null, 'fas'
        );

        $dropzoneHtml[] =
            '<span class="actions">' . $uploadGlyph->render() . '&nbsp;' . $plusGlyph->render() . '</span>';

        $dropzoneHtml[] = '</div>';
        $dropzoneHtml[] = '<div class="panel-footer">';
        $dropzoneHtml[] = $this->getTranslation('DropFileHereMessage');
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
            $glyph = new FontAwesomeGlyph('star', ['text-danger', 'fa-xs'], null, 'fas');
            $label .= '<span class="text-danger">&nbsp;' . $glyph->render() . '</span>';
        }

        $this->addElement('static', $elementName . '_static_data', $label, implode(PHP_EOL, $dropzoneHtml));
        $this->addElement('hidden', $elementName . '_upload_data');

        $dropzoneOptionsString = [];

        foreach ($dropzoneOptions as $optionKey => $optionValue)
        {
            $dropzoneOptionsString[] = $optionKey . ': \'' . $optionValue . '\'';
        }

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'Jquery/jquery.file.upload.js'
        )
        );

        $javascriptHtml = [];

        $javascriptHtml[] = '<script>';
        $javascriptHtml[] = '$(document).ready(function() {';
        $javascriptHtml[] =
            '$("#' . $elementName . '-upload-container").fileUpload({' . implode(', ', $dropzoneOptionsString) . '});';
        $javascriptHtml[] = '});';
        $javascriptHtml[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $javascriptHtml));

        $this->addElement('html', '</div>');
    }

    /**
     * @param string[] $attributes
     *
     * @return string[]
     */
    protected function addFormControlToElementAttributes($attributes)
    {
        if (is_array($attributes))
        {
            if (!array_key_exists('class', $attributes))
            {
                $attributes['class'] = 'form-control';
            }
            else
            {
                $classAttributes = $attributes['class'];

                if (!is_array($classAttributes))
                {
                    $classAttributes = explode(' ', $classAttributes);
                }

                if (!in_array('form-control', $classAttributes))
                {
                    array_unshift($classAttributes, 'form-control');
                }

                $attributes['class'] = implode(' ', $classAttributes);
            }
        }

        return $attributes;
    }

    /**
     * @param string $name
     * @param string $label
     */
    public function addImageUploader($name, $label)
    {
        $this->addElement('html', '<div class="image-uploader" id="image-uploader-' . $name . '">');
        $this->addElement(
            'hidden', $name, null, ' id="' . $name . '" data-element="' . $name . '" class="image-uploader-data"'
        );

        $glyph = new FontAwesomeGlyph('image', ['image-uploader-preview', 'fa-10x', 'text-muted'], null, 'fas');

        $this->addElement(
            'static', 'thumbnail', $label,
            '<div class="thumbnail" data-element="' . $name . '">' . $glyph->render() . '</div>'
        );
        $this->addElement('file', $name . '-file', null, 'class="image-uploader-file" data-element="' . $name . '"');

        $this->addElement('html', '</div>');

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'ImageUploader.js'
        )
        );
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $label
     * @param string $message
     * @param bool $noMargin
     */
    protected function addMessage($type, $name, $label, $message, $noMargin = false)
    {
        $html = [];

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
        $buttons = [];

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $this->getTranslation('Save', []), ['class' => 'positive']
        );

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $this->getTranslation('Reset', []), ['class' => 'normal empty']
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @param string $elementName
     * @param string[] $dropzoneOptions
     * @param bool $includeLabel
     */
    public function addSingleFileDropzone($elementName, $dropzoneOptions = [], $includeLabel = true)
    {
        $dropzoneOptions['maxFiles'] = 1;
        $dropzoneOptions['successCallbackFunction'] = 'chamilo.libraries.single.processUploadedFile';
        $dropzoneOptions['removedfileCallbackFunction'] = 'chamilo.libraries.single.deleteUploadedFile';

        $this->addFileDropzone($elementName, $dropzoneOptions, $includeLabel, true);

        $this->disableSubmitButton();

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) .
            'Jquery/jquery.file.upload.single.js'
        )
        );
    }

    /**
     * @param string $elementLabel
     * @param string $fromElementName
     * @param string $toElementName
     * @param string $foreverElementName
     * @param string $elementNamePrefix
     */
    public function addTimePeriodSelection(
        string $elementLabel, string $fromElementName = self::PROPERTY_TIME_PERIOD_FROM_DATE,
        string $toElementName = self::PROPERTY_TIME_PERIOD_TO_DATE,
        string $foreverElementName = self::PROPERTY_TIME_PERIOD_FOREVER, string $elementNamePrefix = null
    )
    {
        if ($elementNamePrefix)
        {
            $foreverElementName = $elementNamePrefix . '[' . $foreverElementName . ']';
            $fromElementName = $elementNamePrefix . '[' . $fromElementName . ']';
            $toElementName = $elementNamePrefix . '[' . $toElementName . ']';
        }

        $choices = [];

        $choices[] = $this->createElement(
            'radio', $foreverElementName, '', $this->getTranslation('Forever'), 1
        );

        $choices[] = $this->createElement(
            'radio', $foreverElementName, '', $this->getTranslation('LimitedPeriod'), 0
        );

        $this->addElement('html', '<div class="form-time-period">');

        $this->addGroup($choices, null, $this->getTranslation($elementLabel), '', false);

        $this->addElement('html', '<div class="form-time-period-dates hidden">');
        $this->add_timewindow($fromElementName, $toElementName, '', '');
        $this->addElement('html', '</div>');

        $this->addElement('html', '</div>');

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'FormTimePeriod.min.js'
        )
        );

        $template = str_replace(
            '<div class="element form-inline">', '<div class="element form-time-period-date form-inline">',
            $this->getDatePickerTemplate()
        );

        $this->get_renderer()->setElementTemplate($template, $fromElementName);
        $this->get_renderer()->setElementTemplate($template, $toElementName);
    }

    /**
     * Add a datepicker element to the form A rule is added to check if the date is a valid one
     *
     * @param string $name  The element name
     * @param string $label The label for the form-element
     * @param bool $includeTimePicker
     *
     * @return \Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_datepicker
     */
    public function add_datepicker($name, $label, $includeTimePicker = true)
    {
        $element = $this->addElement(
            'datepicker', $this->getAttribute('name'), $name, $label, ['class' => $name], $includeTimePicker
        );
        $this->addRule($name, $this->getTranslation('InvalidDate'), 'date');

        $this->get_renderer()->setElementTemplate($this->getDatePickerTemplate(), $name);

        return $element;
    }

    /**
     * Adds an error message to the form.
     *
     * @param string $name
     * @param string $label
     * @param string $message
     * @param bool $noMargin
     */
    public function add_error_message($name, $label, $message, $noMargin = false)
    {
        return $this->addMessage('danger', $name, $label, $message, $noMargin);
    }

    /**
     * Add a HTML-editor to the form to fill in a title.
     * A trim-filter is attached to the field. A HTML-filter is
     * attached to the field (cleans HTML) A rule is attached to check for unwanted HTML
     *
     * @param string $name
     * @param string $label
     * @param bool $required
     * @param string[] $options
     * @param string[] $attributes
     */
    public function add_html_editor($name, $label, $required = true, $options = [], $attributes = [])
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
     * @param bool $noMargin
     */
    public function add_information_message($name, $label, $message, $noMargin = false)
    {
        return $this->addMessage('info', $name, $label, $message, $noMargin);
    }

    /**
     * Add a password field to the form.
     *
     * @param string $name
     * @param string $label
     * @param bool $required
     * @param string[] $attributes
     *
     * @return \HTML_QuickForm_password
     */
    public function add_password($name, $label, $required = true, $attributes = [])
    {
        /**
         * @var \HTML_QuickForm_password $element
         */
        $element = $this->addElement($this->create_password($name, $label, $attributes));

        if ($required)
        {
            $this->addRule(
                $name, $this->getTranslation('ThisFieldIsRequired', []), 'required'
            );
        }

        return $element;
    }

    /**
     * Adds a select control to the form.
     *
     * @param string $name         The element name.
     * @param string $label        The element label.
     * @param string[] $values     Associative array of possible values.
     * @param bool $required       <code>true</code> if required (default), <code>false</code> otherwise.
     * @param string[] $attributes Element attributes (optional).
     *
     * @return \HTML_QuickForm_select The element.
     */
    public function add_select($name, $label, $values, $required = true, $attributes = [])
    {
        /**
         * @var \HTML_QuickForm_select $element
         */
        $element = $this->addElement($this->create_select($name, $label, $values, $attributes));

        if ($required)
        {
            $this->addRule(
                $name, $this->getTranslation('ThisFieldIsRequired', []), 'required'
            );
        }

        return $element;
    }

    /**
     * Add a textfield to the form.
     * A trim-filter is attached to the field.
     *
     * @param string $name         The element name
     * @param string $label        The label for the form-element
     * @param bool $required       Is the form-element required (default=true)
     * @param string[] $attributes Optional list of attributes for the form-element
     *
     * @return \HTML_QuickForm_text The element.
     * @throws \QuickformException
     */
    public function add_textfield($name, $label, $required = true, $attributes = [])
    {
        /**
         * @var \HTML_QuickForm_text $element
         */
        $element = $this->addElement($this->create_textfield($name, $label, $attributes));

        $this->applyFilter($name, 'trim');

        if ($required)
        {
            $this->addRule(
                $name, $this->getTranslation('ThisFieldIsRequired', []), 'required'
            );
        }

        return $element;
    }

    /**
     * Add a timewindow element to the form.
     * 2 datepicker elements are added and a rule to check if the first date is
     * before the second one.
     *
     * @param string $firstName   The element name
     * @param string $secondName  The element name
     * @param string $firstLabel  The label for the form-element
     * @param string $secondLabel The label for the form-element
     * @param bool $includeTimePicker
     *
     * @return \Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_datepicker[]
     */
    public function add_timewindow($firstName, $secondName, $firstLabel, $secondLabel, $includeTimePicker = true)
    {
        $elements = [];

        $elements[] = $this->add_datepicker($firstName, $firstLabel, $includeTimePicker);
        $elements[] = $this->add_datepicker($secondName, $secondLabel, $includeTimePicker);

        $this->addRule(
            [$firstName, $secondName], $this->getTranslation('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte'
        );

        return $elements;
    }

    /**
     * Adds a warning message to the form.
     *
     * @param string $name
     * @param string $label
     * @param string $message
     * @param bool $noMargin
     */
    public function add_warning_message($name, $label, $message, $noMargin = false)
    {
        return $this->addMessage('warning', $name, $label, $message, $noMargin);
    }

    /**
     * @param \HTML_QuickForm_element $elements
     * @param string $name
     * @param string $groupLabel
     * @param string $separator
     * @param bool $appendName
     *
     * @return \HTML_QuickForm_group
     */
    public function createGroup($elements, $name = null, $groupLabel = '', $separator = null, $appendName = true)
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
     * @param string $name
     * @param string $label
     * @param string[] $options
     * @param string[] $attributes
     *
     * @return \HTML_QuickForm_textarea
     */
    public function create_html_editor($name, $label, $options = [], $attributes = [])
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
    public function create_password($name, $label, $attributes = [])
    {
        $attributes = $this->addFormControlToElementAttributes($attributes);

        return $this->createElement('password', $name, $label, $attributes);
    }

    public function create_select($name, $label, $values, $attributes = [])
    {
        $attributes = $this->addFormControlToElementAttributes($attributes);

        return $this->createElement('select', $name, $label, $values, $attributes);
    }

    /**
     * @param string $name
     * @param string $label
     * @param string[] $attributes
     *
     * @return \HTML_QuickForm_text
     */
    public function create_textfield($name, $label, $attributes = [])
    {
        $attributes = $this->addFormControlToElementAttributes($attributes);

        return $this->createElement('text', $name, $label, $attributes);
    }

    /**
     * Disables the submit button
     */
    protected function disableSubmitButton()
    {
        $javascriptHtml = [];

        $javascriptHtml[] = '<script>';
        $javascriptHtml[] = '$(document).ready(function() {';
        $javascriptHtml[] = '$(\'button[type=submit]\').prop(\'disabled\', true)';
        $javascriptHtml[] = '});';
        $javascriptHtml[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $javascriptHtml));
    }

    public function exportValues($elementList = null): array
    {
        $values = parent::exportValues($elementList);
        $values[self::PROPERTY_HTML_EDITORS] = $this->get_html_editors();

        return $values;
    }

    protected function getDatePickerTemplate()
    {
        return str_replace('<div class="element">', '<div class="element form-inline">', $this->getElementTemplate());
    }

    /**
     * @param string $extraClasses
     *
     * @return string
     */
    public function getElementTemplate($extraClasses = null)
    {
        $html = [];
        $glyph = new FontAwesomeGlyph('star', ['text-danger', 'fa-xs'], null, 'fas');

        $html[] = '<div class="form-row row ' . $extraClasses . '">';
        $html[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label control-label">';
        $html[] = '{label}<!-- BEGIN required --><span class="text-danger">&nbsp;' . $glyph->render() .
            '</span> <!-- END required -->';
        $html[] = '</div>';
        $html[] = '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">';
        $html[] =
            '<div class="element"><!-- BEGIN error --><small class="text-danger">{error}</small><br /><!-- END error -->	{element}</div>';
        $html[] = '<div class="form_feedback"></div></div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function getFormTabsGenerator(): FormTabsGenerator
    {
        return $this->getService(FormTabsGenerator::class);
    }

    /**
     * @return string
     */
    public function getFormTemplate()
    {
        $html = [];

        $html[] = '<form {attributes}>';
        $html[] = '{content}';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</form>';

        return implode(PHP_EOL, $html);
    }

    public function getRequiredNoteTemplate()
    {
        $html = [];

        $html[] = '<div class="form-row row">';
        $html[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label"></div>';
        $html[] = '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">{requiredNote}</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Platform\Security
     */
    private function getSecurity(): Security
    {
        return $this->getService(Security::class);
    }

    /**
     * Helper Function
     *
     * @param string $variable
     * @param string[] $parameters
     * @param string $context
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = [], $context = StringUtilities::LIBRARIES)
    {
        return $this->getTranslator()->trans($variable, $parameters, $context);
    }

    /**
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
     * Formats an multiple dimension array to a single dimension array to support default values in the quickform
     * library because quickform produces arrays when an array is used in the name, but quickform does not accept arrays
     * for the default values, instead the inner arrays are converted as strings
     *
     * @param string[][] $array
     * @param int $level
     *
     * @return string[]
     */
    protected function multi_dimensional_array_to_single_dimensional_array($array, $level = 0)
    {
        $single_dimension_array = [];

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
     * @param int $value
     *
     * @return int
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

    public function registerAdditionalElements(): void
    {
        // Date and timepicker elements
        static::registerElementType('datepicker', HTML_QuickForm_datepicker::class);

        // Element finder elements
        static::registerElementType('advanced_element_finder', HTML_QuickForm_advanced_element_finder::class);

        // Button elements
        static::registerElementType('style_button', HTML_QuickForm_stylebutton::class);
        static::registerElementType('style_submit_button', HTML_QuickForm_stylesubmitbutton::class);
        static::registerElementType('style_reset_button', HTML_QuickForm_styleresetbutton::class);

        // Replacing some default elements
        static::registerElementType('radio', HTML_QuickForm_bootstrap_radio::class);
        static::registerElementType('checkbox', HTML_QuickForm_extended_checkbox::class);
        static::registerElementType('file', HTML_QuickForm_stylefile::class);
        static::registerElementType('toggle', HTML_QuickForm_toggle::class);
        static::registerElementType('category', HTML_QuickForm_category::class);
    }

    public function registerAdditionalRules(): void
    {
        static::registerRule('date', null, HTML_QuickForm_Rule_Date::class);
        static::registerRule('date_compare', null, HTML_QuickForm_Rule_DateCompare::class);
        static::registerRule('number_compare', null, HTML_QuickForm_Rule_NumberCompare::class);
        static::registerRule('username_available', null, HTML_QuickForm_Rule_UsernameAvailable::class);
        static::registerRule('username', null, HTML_QuickForm_Rule_Username::class);
        static::registerRule('filetype', null, HTML_QuickForm_Rule_Filetype::class);
        static::registerRule(
            'validate_database_connection', null, HTML_QuickForm_Rule_ValidateDatabaseConnection::class
        );
    }

    /**
     * @param string $name
     */
    public function register_html_editor($name)
    {
        $this->html_editors[] = $name;
    }

    public function setDefaultTemplates()
    {
        $glyph = new FontAwesomeGlyph('star', ['text-danger', 'fa-xs'], null, 'fas');

        HTML_QuickForm::setRequiredNote(
            '<span class="text-danger">&nbsp;' . $glyph->render() . '&nbsp;<small>' .
            $this->getTranslation('ThisFieldIsRequired', []) . '</small></span>'
        );

        $this->renderer = $this->defaultRenderer();

        $this->renderer->setFormTemplate($this->getFormTemplate());
        $this->renderer->setElementTemplate($this->getElementTemplate());
        $this->renderer->setRequiredNoteTemplate($this->getRequiredNoteTemplate());
    }

    /**
     * @param \HTML_QuickForm_Renderer_Default $renderer
     */
    public function set_renderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Returns the HTML representation of this form.
     *
     * @param ?string $in_data
     *
     * @return string
     * @deprecated Use render() now
     */
    public function toHtml(?string $in_data = null): string
    {
        return $this->render($in_data);
    }

    /**
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
}
