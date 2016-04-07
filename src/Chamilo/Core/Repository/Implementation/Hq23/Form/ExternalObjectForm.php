<?php
namespace Chamilo\Core\Repository\Implementation\Hq23\Form;

use Chamilo\Core\Repository\Implementation\Hq23\ExternalObject;
use Chamilo\Core\Repository\Implementation\Hq23\ExternalObjectDisplay;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: hq23_external_repository_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 *
 * @package
 *
 *
 *
 *
 *
 */
class ExternalObjectForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const PREVIEW = 'preview';
    const FILE = 'file';

    private $application;

    private $form_type;

    private $external_repository_object;

    public function __construct($form_type, $action, $application)
    {
        parent :: __construct(ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true), 'post', $action);

        $this->application = $application;

        $this->form_type = $form_type;

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;

        $defaults[ExternalObject :: PROPERTY_ID] = $external_repository_object->get_id();
        // $defaults[ExternalObject :: PROPERTY_TITLE] = $external_repository_object->get_title();
        $defaults[ExternalObject :: PROPERTY_DESCRIPTION] = html_entity_decode(
            $external_repository_object->get_description());
        $defaults[ExternalObject :: PROPERTY_TAGS] = $external_repository_object->get_tags_string(false);

        $display = ExternalObjectDisplay :: factory($external_repository_object);
        $defaults[self :: PREVIEW] = $display->get_preview();

        parent :: setDefaults($defaults);
    }

    public function get_tags()
    {
        $external_repository_object = $this->external_repository_object;
        return implode(",", $external_repository_object->get_tags());
    }

    public function build_basic_form()
    {
        // $this->addElement('text', ExternalObject :: PROPERTY_TITLE, Translation :: get('Title'),
        // array('size' => '50'));
        // $this->addRule(ExternalObject :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'),
        // 'required');
        $this->addElement(
            'textarea',
            ExternalObject :: PROPERTY_TAGS,
            Translation :: get('Tags'),
            array('rows' => '2', 'cols' => '80'));

        $this->addElement(
            'textarea',
            ExternalObject :: PROPERTY_DESCRIPTION,
            Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES),
            array('rows' => '7', 'cols' => '80'));
    }

    public function build_editing_form()
    {
        $this->addElement('static', self :: PREVIEW);

        $this->build_basic_form();

        $this->addElement('hidden', ExternalObject :: PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_photo()
    {
        return $this->application->get_external_repository_manager_connector()->update_external_repository_object(
            $this->exportValues());
    }

    public function upload_photo()
    {
        if (StringUtilities :: getInstance()->hasValue(($_FILES[self :: FILE]['name'])))
        {
            return $this->application->get_external_repository_manager_connector()->create_external_repository_object(
                $this->exportValues(),
                $_FILES[self :: FILE]['tmp_name']);
        }
        else
        {
            return false;
        }
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $this->addElement('file', self :: FILE, Translation :: get('FileName'));

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
}
