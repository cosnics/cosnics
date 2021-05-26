<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Form;

use Chamilo\Core\Repository\Implementation\Youtube\ExternalObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalObjectForm extends FormValidator
{
    const FILE = 'file';

    const RESULT_ERROR = 'GroupUpdateFailed';

    const RESULT_SUCCESS = 'GroupUpdated';

    const TYPE_CREATE = 1;

    const TYPE_EDIT = 2;

    const VIDEO_CATEGORY = 'category';

    const VIDEO_DESCRIPTION = 'description';

    const VIDEO_TAGS = 'tags';

    const VIDEO_TITLE = 'title';

    private $application;

    private $form_type;

    private $external_repository_object;

    public function __construct($form_type, $action, $application)
    {
        parent::__construct('youtube_upload', self::FORM_METHOD_POST, $action);

        $this->application = $application;

        $this->form_type = $form_type;

        if ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_uploading_form();
        }
        elseif ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_edit_form();
        }

        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement(
            'text', ExternalObject::PROPERTY_TITLE, Translation::get('Title', null, Utilities::COMMON_LIBRARIES),
            array("size" => "50")
        );
        $this->addRule(
            ExternalObject::PROPERTY_TITLE, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement(
            'select', ExternalObject::PROPERTY_CATEGORY,
            Translation::get('Category', null, Utilities::COMMON_LIBRARIES), $this->get_youtube_categories()
        );

        $this->addElement(
            'textarea', ExternalObject::PROPERTY_TAGS, Translation::get('Tags'), array("rows" => "2", "cols" => "80")
        );
        $this->addRule(
            ExternalObject::PROPERTY_TAGS, Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement(
            'textarea', ExternalObject::PROPERTY_DESCRIPTION,
            Translation::get('Description', null, Utilities::COMMON_LIBRARIES), array("rows" => "7", "cols" => "80")
        );
    }

    public function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', ExternalObject::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_uploading_form()
    {
        $this->build_basic_form();

        $this->addElement('file', self::FILE, sprintf(Translation::get('FileName'), '2Gb'));

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Upload', null, Utilities::COMMON_LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function get_tags()
    {
        $external_repository_object = $this->external_repository_object;
        $tags = $external_repository_object->get_tags();

        return implode(",", $tags);
    }

    public function get_youtube_categories()
    {
        $youtube = $this->application->get_external_repository_manager_connector();

        return $youtube->retrieve_categories();
    }

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;

        $defaults[ExternalObject::PROPERTY_TITLE] = $external_repository_object->get_title();
        $defaults[ExternalObject::PROPERTY_DESCRIPTION] = $external_repository_object->get_description();
        $defaults[ExternalObject::PROPERTY_CATEGORY] = $external_repository_object->get_category();
        $defaults[ExternalObject::PROPERTY_TAGS] = $this->get_tags();

        parent::setDefaults($defaults);
    }

    public function update_video()
    {
        return $this->application->get_external_repository_manager_connector()->update_youtube_video(
            $this->exportValues()
        );
    }

    public function upload_video()
    {
        if (StringUtilities::getInstance()->hasValue(($_FILES[self::FILE]['name'])))
        {
            return $this->application->get_external_repository_manager_connector()->upload_video(
                $this->exportValues(), $_FILES[self::FILE]
            );
        }
        else
        {
            return false;
        }
    }
}
