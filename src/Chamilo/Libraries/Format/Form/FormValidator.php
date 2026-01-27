<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
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
use Chamilo\Libraries\Format\Form\Rule\HTML_QuickForm_Rule_Username;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Form\FormTabsGenerator;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_QuickForm;
use HTML_QuickForm_element;
use HTML_QuickForm_group;
use HTML_QuickForm_hidden;
use HTML_QuickForm_html;
use HTML_QuickForm_password;
use HTML_QuickForm_Renderer_Default;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_select;
use HTML_QuickForm_static;
use HTML_QuickForm_text;
use HTML_QuickForm_textarea;

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
     * @var string[]
     */
    private array $html_editors = [];

    /**
     * @var \HTML_QuickForm_Renderer_Default
     */
    private HTML_QuickForm_Renderer_Default $renderer;

    /**
     * Constructor
     *
     * @param string $formName Name of the form
     * @param string $method Method (FormValidator::FORM_METHOD_POST (default) or FormValidator::FORM_METHOD_GET)
     * @param string $action Action (default is $PHP_SELF)
     * @param string $target Form's target defaults to '_self'
     * @param string[] $attributes (optional)Extra attributes for <form> tag
     * @param bool $trackSubmit (optional)Whether to track if the form was submitted by adding a special hidden field
     *                             (default = true)
     *
     * @throws \QuickformException
     */
    public function __construct(
        string $formName = '', string $method = self::FORM_METHOD_POST, string $action = '', string $target = '',
        array $attributes = [], bool $trackSubmit = true
    )
    {
        $attributes['onreset'] = 'resetElements()';

        parent::__construct($formName, $method, $action, $target, $attributes, $trackSubmit);

        $this->registerAdditionalElements();
        $this->registerAdditionalRules();

        $this->addElement(
            HTML_QuickForm_html::class, $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'Reset.js'
        )
        );

        foreach ($this->_submitValues as & $value)
        {
            $value = $this->getSecurity()->removeXSS($value);
        }

        $this->setDefaultTemplates();
    }

    /**
     * @throws \QuickformException
     */
    public function render(?string $in_data = null): string
    {
        $error = false;

        foreach ($this->_elements as $element)
        {
            if ($element->getName() && !is_null(parent::getElementError($element->getName())))
            {
                $error = true;
                break;
            }
        }

        $html = [];

        if ($error)
        {
            $html[] = $this->getNotificationMessageRenderer()->renderOne(
                new NotificationMessage(
                    $this->getTranslation('FormHasErrorsPleaseComplete'), NotificationMessage::TYPE_DANGER
                ), false
            );
        }

        $html[] = parent::toHtml($in_data);

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function addDatepicker(string $name, string $label, bool $includeTimePicker = true
    ): HTML_QuickForm_element|HTML_QuickForm_datepicker
    {
        $element = $this->addElement(
            HTML_QuickForm_datepicker::class, $this->getAttribute('name'), $name, $label, ['class' => $name],
            $includeTimePicker
        );
        $this->addRule($name, $this->getTranslation('InvalidDate'), HTML_QuickForm_Rule_Date::class);

        $this->getRenderer()->setElementTemplate($this->getDatePickerTemplate(), $name);

        return $element;
    }

    /**
     * @throws \QuickformException
     */
    public function addErrorMessage(string $name, ?string $label, string $message, bool $noMargin = false
    ): HTML_QuickForm_html
    {
        return $this->addMessage('danger', $name, $label, $message, $noMargin);
    }

    /**
     * @throws \QuickformException
     */
    public function addFileDropzone(
        string $elementName, array $dropzoneOptions = [], bool $includeLabel = true, bool $markRequired = false
    ): void
    {
        if (array_key_exists('autoProcessQueue', $dropzoneOptions))
        {
            if ($dropzoneOptions['autoProcessQueue'] === false)
            {
                $dropzoneOptions['autoProcessQueue'] = 'false';
            }
        }

        $this->addElement(HTML_QuickForm_html::class, '<div id="' . $elementName . '-upload-container">');

        $this->addElement(HTML_QuickForm_html::class, '<div id="' . $elementName . '-upload-input">');
        $this->addElement(HTML_QuickForm_stylefile::class, $elementName, $this->getTranslation('FileName'));
        $this->addElement(HTML_QuickForm_html::class, '</div>');

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

        $this->addElement(
            HTML_QuickForm_static::class, $elementName . '_static_data', $label, implode(PHP_EOL, $dropzoneHtml)
        );
        $this->addElement(HTML_QuickForm_hidden::class, $elementName . '_upload_data');

        $dropzoneOptionsString = [];

        foreach ($dropzoneOptions as $optionKey => $optionValue)
        {
            $dropzoneOptionsString[] = $optionKey . ': \'' . $optionValue . '\'';
        }

        $this->addElement(
            HTML_QuickForm_html::class, $this->getResourceManager()->getResourceHtml(
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

        $this->addElement(HTML_QuickForm_html::class, implode(PHP_EOL, $javascriptHtml));

        $this->addElement(HTML_QuickForm_html::class, '</div>');
    }

    /**
     * @param string[] $attributes
     *
     * @return string[]
     */
    protected function addFormControlToElementAttributes(array $attributes = []): array
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

        return $attributes;
    }

    /**
     * @param string[] $options
     * @param string[] $attributes
     *
     * @throws \QuickformException
     */
    public function addHtmlEditor(
        string $name, string $label, bool $required = true, array $options = [], array $attributes = []
    ): void
    {
        $formValidatorHtmlEditorRenderer = $this->getFormValidatorHtmlEditorRenderer();
        $formValidatorHtmlEditorRenderer->addHtmlEditor($this, $name, $label, $required, $options, $attributes);
    }

    /**
     * @throws \QuickformException
     */
    public function addImageUploader(string $name, string $label): void
    {
        $this->addElement(HTML_QuickForm_html::class, '<div class="image-uploader" id="image-uploader-' . $name . '">');
        $this->addElement(
            HTML_QuickForm_hidden::class, $name, null,
            ' id="' . $name . '" data-element="' . $name . '" class="image-uploader-data"'
        );

        $glyph = new FontAwesomeGlyph('image', ['image-uploader-preview', 'fa-10x', 'text-muted'], null, 'fas');

        $this->addElement(
            HTML_QuickForm_static::class, 'thumbnail', $label,
            '<div class="thumbnail" data-element="' . $name . '">' . $glyph->render() . '</div>'
        );
        $this->addElement(
            HTML_QuickForm_stylefile::class, $name . '-file', null,
            'class="image-uploader-file" data-element="' . $name . '"'
        );

        $this->addElement(HTML_QuickForm_html::class, '</div>');

        $this->addElement(
            HTML_QuickForm_html::class, $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'ImageUploader.js'
        )
        );
    }

    /**
     * @throws \QuickformException
     */
    public function addInformationMessage(string $name, ?string $label, string $message, bool $noMargin = false
    ): HTML_QuickForm_html
    {
        return $this->addMessage('info', $name, $label, $message, $noMargin);
    }

    /**
     * @throws \QuickformException
     */
    protected function addMessage(string $type, string $name, ?string $label, string $message, bool $noMargin = false
    ): HTML_QuickForm_html
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

        return $this->addElement(HTML_QuickForm_html::class, implode(PHP_EOL, $html));
    }

    /**
     * @param string[] $attributes
     *
     * @throws \QuickformException
     */
    public function addPassword(string $name, string $label, bool $required = true, array $attributes = []
    ): HTML_QuickForm_password
    {
        $element = $this->addElement($this->createPassword($name, $label, $attributes));

        if ($required)
        {
            $this->addRule(
                $name, $this->getTranslation('ThisFieldIsRequired'), HTML_QuickForm_Rule_Required::class
            );
        }

        return $element;
    }

    /**
     * @throws \QuickformException
     */
    public function addSaveResetButtons(): HTML_QuickForm_group
    {
        $buttons = [];

        $buttons[] = $this->createElement(
            HTML_QuickForm_stylesubmitbutton::class, 'submit', $this->getTranslation('Save'), ['class' => 'positive']
        );

        $buttons[] = $this->createElement(
            HTML_QuickForm_styleresetbutton::class, 'reset', $this->getTranslation('Reset'), ['class' => 'normal empty']
        );

        return $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @param string[] $values Associative array of possible values.
     * @param string[] $attributes Element attributes (optional).
     *
     * @throws \QuickformException
     */
    public function addSelect(string $name, string $label, array $values, bool $required = true, array $attributes = []
    ): HTML_QuickForm_select
    {
        $element = $this->addElement($this->createSelect($name, $label, $values, $attributes));

        if ($required)
        {
            $this->addRule(
                $name, $this->getTranslation('ThisFieldIsRequired'), HTML_QuickForm_Rule_Required::class
            );
        }

        return $element;
    }

    /**
     * @throws \QuickformException
     */
    public function addSingleFileDropzone(string $elementName, array $dropzoneOptions = [], bool $includeLabel = true
    ): void
    {
        $dropzoneOptions['maxFiles'] = 1;
        $dropzoneOptions['successCallbackFunction'] = 'chamilo.libraries.single.processUploadedFile';
        $dropzoneOptions['removedfileCallbackFunction'] = 'chamilo.libraries.single.deleteUploadedFile';

        $this->addFileDropzone($elementName, $dropzoneOptions, $includeLabel, true);

        $this->disableSubmitButton();

        $this->addElement(
            HTML_QuickForm_html::class, $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) .
            'Jquery/jquery.file.upload.single.js'
        )
        );
    }

    /**
     * @param string[] $attributes Optional list of attributes for the form-element
     *
     * @throws \QuickformException
     */
    public function addTextfield(string $name, string $label, bool $required = true, array $attributes = []
    ): HTML_QuickForm_text
    {
        $element = $this->addElement($this->create_textfield($name, $label, $attributes));

        $this->applyFilter($name, 'trim');

        if ($required)
        {
            $this->addRule(
                $name, $this->getTranslation('ThisFieldIsRequired'), HTML_QuickForm_Rule_Required::class
            );
        }

        return $element;
    }

    /**
     * @throws \QuickformException
     */
    public function addTimePeriodSelection(
        string $elementLabel, string $fromElementName = self::PROPERTY_TIME_PERIOD_FROM_DATE,
        string $toElementName = self::PROPERTY_TIME_PERIOD_TO_DATE,
        string $foreverElementName = self::PROPERTY_TIME_PERIOD_FOREVER, string $elementNamePrefix = null
    ): void
    {
        if ($elementNamePrefix)
        {
            $foreverElementName = $elementNamePrefix . '[' . $foreverElementName . ']';
            $fromElementName = $elementNamePrefix . '[' . $fromElementName . ']';
            $toElementName = $elementNamePrefix . '[' . $toElementName . ']';
        }

        $choices = [];

        $choices[] = $this->createElement(
            HTML_QuickForm_bootstrap_radio::class, $foreverElementName, '', $this->getTranslation('Forever'), 1
        );

        $choices[] = $this->createElement(
            HTML_QuickForm_bootstrap_radio::class, $foreverElementName, '', $this->getTranslation('LimitedPeriod'), 0
        );

        $this->addElement(HTML_QuickForm_html::class, '<div class="form-time-period">');

        $this->addGroup($choices, null, $this->getTranslation($elementLabel), '', false);

        $this->addElement(HTML_QuickForm_html::class, '<div class="form-time-period-dates hidden">');
        $this->addTimewindow($fromElementName, $toElementName, '', '');
        $this->addElement(HTML_QuickForm_html::class, '</div>');

        $this->addElement(HTML_QuickForm_html::class, '</div>');

        $this->addElement(
            HTML_QuickForm_html::class, $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(StringUtilities::LIBRARIES) . 'FormTimePeriod.min.js'
        )
        );

        $template = str_replace(
            '<div class="element form-inline">', '<div class="element form-time-period-date form-inline">',
            $this->getDatePickerTemplate()
        );

        $this->getRenderer()->setElementTemplate($template, $fromElementName);
        $this->getRenderer()->setElementTemplate($template, $toElementName);
    }

    /**
     * @return \Chamilo\Libraries\Format\Form\Element\HTML_QuickForm_datepicker[]
     * @throws \QuickformException
     */
    public function addTimewindow(
        string $firstName, string $secondName, string $firstLabel, string $secondLabel, bool $includeTimePicker = true
    ): array
    {
        $elements = [];

        $elements[] = $this->addDatepicker($firstName, $firstLabel, $includeTimePicker);
        $elements[] = $this->addDatepicker($secondName, $secondLabel, $includeTimePicker);

        $this->addRule(
            [$firstName, $secondName], $this->getTranslation('StartDateShouldBeBeforeEndDate'),
            HTML_QuickForm_Rule_DateCompare::class, 'lte'
        );

        return $elements;
    }

    /**
     * @throws \QuickformException
     */
    public function addWarningMessage(string $name, ?string $label, string $message, bool $noMargin = false
    ): HTML_QuickForm_html
    {
        return $this->addMessage('warning', $name, $label, $message, $noMargin);
    }

    /**
     * @param \HTML_QuickForm_element[] $elements
     *
     * @throws \QuickformException
     */
    public function createGroup(
        array $elements, ?string $name = null, string $groupLabel = '', ?string $separator = null,
        bool $appendName = true
    ): HTML_QuickForm_group
    {
        static $anonGroups = 1;

        if (0 == strlen($name))
        {
            $name = 'qf_group_' . $anonGroups ++;
            $appendName = false;
        }

        return $this->createElement(
            HTML_QuickForm_group::class, $name, $groupLabel, $elements, $separator, $appendName
        );
    }

    /**
     * @param string[] $options
     * @param string[] $attributes
     *
     * @throws \QuickformException
     */
    public function createHtmlEditor(string $name, string $label, array $options = [], array $attributes = []
    ): HTML_QuickForm_textarea
    {
        $htmlEditorOptionsFactory = $this->getFormValidatorHtmlEditorOptionsFactory();

        return $this->getFormValidatorHtmlEditorRenderer()->createHtmlEditor(
            $this, $name, $label, $htmlEditorOptionsFactory->getDefaultFormValidatorHtmlEditorOptions($options),
            $attributes
        );
    }

    /**
     * @param string[] $attributes
     *
     * @throws \QuickformException
     */
    public function createPassword(string $name, string $label, array $attributes = []): HTML_QuickForm_password
    {
        $attributes = $this->addFormControlToElementAttributes($attributes);

        return $this->createElement(HTML_QuickForm_password::class, $name, $label, $attributes);
    }

    /**
     * @throws \QuickformException
     */
    public function createSelect($name, $label, $values, $attributes = []): HTML_QuickForm_select
    {
        $attributes = $this->addFormControlToElementAttributes($attributes);

        return $this->createElement(HTML_QuickForm_select::class, $name, $label, $values, $attributes);
    }

    /**
     * @param string[] $attributes
     *
     * @throws \QuickformException
     */
    public function create_textfield(string $name, string $label, array $attributes = []): HTML_QuickForm_text
    {
        $attributes = $this->addFormControlToElementAttributes($attributes);

        return $this->createElement(HTML_QuickForm_text::class, $name, $label, $attributes);
    }

    /**
     * @throws \QuickformException
     */
    protected function disableSubmitButton(): void
    {
        $javascriptHtml = [];

        $javascriptHtml[] = '<script>';
        $javascriptHtml[] = '$(document).ready(function() {';
        $javascriptHtml[] = '$(\'button[type=submit]\').prop(\'disabled\', true)';
        $javascriptHtml[] = '});';
        $javascriptHtml[] = '</script>';

        $this->addElement(HTML_QuickForm_html::class, implode(PHP_EOL, $javascriptHtml));
    }

    public function exportValues($elementList = null): array
    {
        $values = parent::exportValues($elementList);
        $values[self::PROPERTY_HTML_EDITORS] = $this->getHtmlEditors();

        return $values;
    }

    protected function getDatePickerTemplate(): string
    {
        return str_replace('<div class="element">', '<div class="element form-inline">', $this->getElementTemplate());
    }

    public function getElementTemplate(?string $extraClasses = null): string
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

    public function getFormTemplate(): string
    {
        $html = [];

        $html[] = '<form {attributes}>';
        $html[] = '{content}';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</form>';

        return implode(PHP_EOL, $html);
    }

    protected function getFormValidatorHtmlEditorOptionsFactory(): FormValidatorHtmlEditorOptionsFactory
    {
        return $this->getService(FormValidatorHtmlEditorOptionsFactory::class);
    }

    protected function getFormValidatorHtmlEditorRenderer(): FormValidatorHtmlEditorRenderer
    {
        return $this->getService(FormValidatorHtmlEditorRenderer::class);
    }

    /**
     * @return string[]
     */
    public function getHtmlEditors(): array
    {
        return $this->html_editors;
    }

    public function getRenderer(): HTML_QuickForm_Renderer_Default
    {
        return $this->renderer;
    }

    public function setRenderer(HTML_QuickForm_Renderer_Default $renderer): void
    {
        $this->renderer = $renderer;
    }

    public function getRequiredNoteTemplate(): string
    {
        $html = [];

        $html[] = '<div class="form-row row">';
        $html[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label"></div>';
        $html[] = '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">{requiredNote}</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    private function getSecurity(): Security
    {
        return $this->getService(Security::class);
    }

    protected function getTranslation(
        string $variable, array $parameters = [], string $context = StringUtilities::LIBRARIES
    ): string
    {
        return $this->getTranslator()->trans($variable, $parameters, $context);
    }

    public function registerAdditionalElements(): void
    {
        // Date and timepicker elements
        static::registerElementType(HTML_QuickForm_datepicker::class);

        // Element finder elements
        static::registerElementType(HTML_QuickForm_advanced_element_finder::class);

        // Button elements
        static::registerElementType(HTML_QuickForm_stylebutton::class);
        static::registerElementType(HTML_QuickForm_stylesubmitbutton::class);
        static::registerElementType(HTML_QuickForm_styleresetbutton::class);

        // Toggle and category elements
        static::registerElementType(HTML_QuickForm_toggle::class);
        static::registerElementType(HTML_QuickForm_category::class);

        // Replacing some default elements
        static::registerElementType(HTML_QuickForm_bootstrap_radio::class);
        static::registerElementType(HTML_QuickForm_extended_checkbox::class);
        static::registerElementType(HTML_QuickForm_stylefile::class);
    }

    public function registerAdditionalRules(): void
    {
        static::registerRule(HTML_QuickForm_Rule_Date::class);
        static::registerRule(HTML_QuickForm_Rule_DateCompare::class);
        static::registerRule(HTML_QuickForm_Rule_Username::class);
        static::registerRule(HTML_QuickForm_Rule_Filetype::class);
    }

    public function registerHtmlEditor(string $name): void
    {
        $this->html_editors[] = $name;
    }

    public function setDefaultTemplates(): void
    {
        $glyph = new FontAwesomeGlyph('star', ['text-danger', 'fa-xs'], null, 'fas');

        HTML_QuickForm::setRequiredNote(
            '<span class="text-danger">&nbsp;' . $glyph->render() . '&nbsp;<small>' .
            $this->getTranslation('ThisFieldIsRequired') . '</small></span>'
        );

        $this->renderer = $this->defaultRenderer();

        $this->renderer->setFormTemplate($this->getFormTemplate());
        $this->renderer->setElementTemplate($this->getElementTemplate());
        $this->renderer->setRequiredNoteTemplate($this->getRequiredNoteTemplate());
    }

    public function unregisterHtmlEditor(string $name): void
    {
        $key = array_search($name, $this->html_editors);

        if ($key)
        {
            unset($this->html_editors[$key]);
        }
    }
}
