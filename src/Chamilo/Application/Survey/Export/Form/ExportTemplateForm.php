<?php
namespace Chamilo\Application\Survey\Export\Form;

use Chamilo\Application\Survey\Export\Storage\DataClass\ExportTemplate;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ExportTemplateForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ExportTemplateUpdated';
    const RESULT_ERROR = 'ExportTemplateUpdateFailed';

    private $export_template;

    function __construct($form_type, $action, $export_template)
    {
        parent :: __construct('create_export_template', 'post', $action);

        $this->export_template = $export_template;

        if ($form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement(
            'text',
            ExportTemplate :: PROPERTY_NAME,
            Translation :: get('TemplateName'),
            array("size" => "50"));
        $this->addRule(ExportTemplate :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(ExportTemplate :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
    }

    function build_editing_form()
    {
        $export_template = $this->export_template;

        $this->build_basic_form();
        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update()
    {
        $export_template = $this->export_template;
        $values = $this->exportValues();
        $export_template->set_name($values[ExportTemplate :: PROPERTY_NAME]);
        $export_template->set_description($values[ExportTemplate :: PROPERTY_DESCRIPTION]);

        return $export_template->update();
    }

    function create()
    {
        $export_template = $this->export_template;
        $values = $this->exportValues();
        $export_template->set_name($values[ExportTemplate :: PROPERTY_NAME]);
        $export_template->set_description($values[ExportTemplate :: PROPERTY_DESCRIPTION]);

        return $export_template->create();
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $export_template = $this->export_template;
        $defaults[ExportTemplate :: PROPERTY_NAME] = $export_template->get_name();
        $defaults[ExportTemplate :: PROPERTY_DESCRIPTION] = $export_template->get_description();
        parent :: setDefaults($defaults);
    }

    function get_export_template()
    {
        return $this->export_template;
    }
}
?>