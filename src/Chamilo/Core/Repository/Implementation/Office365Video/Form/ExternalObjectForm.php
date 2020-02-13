<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video\Form;

use Chamilo\Core\Repository\Implementation\Office365Video\ExternalObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalObjectForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'GroupUpdated';
    const RESULT_ERROR = 'GroupUpdateFailed';
    const VIDEO_TITLE = 'title';
    const VIDEO_CATEGORY = 'category';
    const VIDEO_TAGS = 'tags';
    const VIDEO_DESCRIPTION = 'description';
    const FILE = 'file';

    private $application;

    private $form_type;

    private $external_repository_object;

    public function __construct($form_type, $action, $application)
    {
        parent::__construct('office365_video_upload', 'post', $action);
        
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

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;
        
        $defaults[ExternalObject::PROPERTY_TITLE] = $external_repository_object->get_title();
        $defaults[ExternalObject::PROPERTY_DESCRIPTION] = $external_repository_object->get_description();
        $defaults[ExternalObject::PROPERTY_CATEGORY] = $external_repository_object->get_category();
        $defaults[ExternalObject::PROPERTY_TAGS] = $this->get_tags();
        
        parent::setDefaults($defaults);
    }

    public function get_tags()
    {
        $external_repository_object = $this->external_repository_object;
        $tags = $external_repository_object->get_tags();
        return implode(",", $tags);
    }

    public function build_basic_form()
    {
        $this->addElement(
            'text', 
            ExternalObject::PROPERTY_TITLE, 
            Translation::get('Title', null, Utilities::COMMON_LIBRARIES), 
            array("size" => "50"));
        $this->addRule(
            ExternalObject::PROPERTY_TITLE, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'select', 
            ExternalObject::PROPERTY_CATEGORY, 
            Translation::get('Category', null, Utilities::COMMON_LIBRARIES), 
            $this->get_office365_video_categories());
        
        $this->addElement(
            'textarea', 
            ExternalObject::PROPERTY_TAGS, 
            Translation::get('Tags'), 
            array("rows" => "2", "cols" => "80"));
        $this->addRule(
            ExternalObject::PROPERTY_TAGS, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'textarea', 
            ExternalObject::PROPERTY_DESCRIPTION, 
            Translation::get('Description', null, Utilities::COMMON_LIBRARIES), 
            array("rows" => "7", "cols" => "80"));
    }

    public function build_upload_form()
    {
        $this->addElement('file', ExternalObject::PROPERTY_FILE, sprintf(Translation::get('FileName'), '2Gb'));
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Upload', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function get_office365_video_categories()
    {
        $dataConnector = $this->application->get_external_repository_manager_connector();
        return $dataConnector->retrieve_categories();
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        
        $this->addElement('hidden', ExternalObject::PROPERTY_ID);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null,
            new FontAwesomeGlyph( 'arrow-right'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_uploading_form()
    {
        $this->build_basic_form();
        
        $this->addElement('file', self::FILE, sprintf(Translation::get('FileName'), '2Gb'));
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Upload', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_video()
    {
        return $this->application->get_external_repository_manager_connector()->update_video($this->exportValues());
    }

    public function upload_video()
    {
        if (StringUtilities::getInstance()->hasValue(($_FILES[self::FILE]['name'])))
        {
            return $this->application->get_external_repository_manager_connector()->upload_video(
                $this->exportValues(), 
                $_FILES[self::FILE]);
        }
        else
        {
            return false;
        }
    }
}
