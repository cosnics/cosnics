<?php
namespace Chamilo\Core\Repository\Implementation\Picasa\Form;

use Chamilo\Core\Repository\Implementation\Picasa\ExternalObject;
use Chamilo\Core\Repository\Implementation\Picasa\ExternalObjectDisplay;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

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
        parent::__construct(ClassnameUtilities::getInstance()->getClassnameFromObject($this, true), 'post', $action);
        
        $this->application = $application;
        
        $this->form_type = $form_type;
        
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;
        
        $defaults[ExternalObject::PROPERTY_ID] = $external_repository_object->get_id();
        $defaults[ExternalObject::PROPERTY_TITLE] = $external_repository_object->get_description();
        $defaults[ExternalObject::PROPERTY_ALBUM_ID] = $external_repository_object->get_album_id();
        $defaults[ExternalObject::PROPERTY_TAGS] = $external_repository_object->get_tags_string();
        
        $display = ExternalObjectDisplay::factory($external_repository_object);
        $defaults[self::PREVIEW] = $display->get_preview();
        
        parent::setDefaults($defaults);
    }

    public function get_tags()
    {
        $external_repository_object = $this->external_repository_object;
        return implode(",", $external_repository_object->get_tags());
    }

    public function build_basic_form()
    {
        $this->addElement(
            'text', 
            ExternalObject::PROPERTY_TITLE, 
            Translation::get('Title', null, Utilities::COMMON_LIBRARIES), 
            array('size' => '50'));
        $this->addRule(
            ExternalObject::PROPERTY_TITLE, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $albums = $this->application->get_external_repository_manager_connector()->get_authenticated_user_albums();
        
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->add_information_message('picasa_api_move', null, Translation::get('PicasaAPIMoveImpossible'));
        }
        
        $this->addElement('select', ExternalObject::PROPERTY_ALBUM_ID, Translation::get('Album'), $albums);
        
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->freeze(ExternalObject::PROPERTY_ALBUM_ID);
        }
        
        $this->addElement(
            'textarea', 
            ExternalObject::PROPERTY_TAGS, 
            Translation::get('Tags'), 
            array('rows' => '7', 'cols' => '80'));
    }

    public function build_editing_form()
    {
        $this->addElement('static', self::PREVIEW);
        
        $this->build_basic_form();
        
        $this->addElement('hidden', ExternalObject::PROPERTY_ID);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_photo()
    {
        return $this->application->get_external_repository_manager_connector()->update_external_repository_object(
            $this->exportValues());
    }

    public function upload_photo()
    {
        if (StringUtilities::getInstance()->hasValue(($_FILES[self::FILE]['name'])))
        {
            return $this->application->get_external_repository_manager_connector()->create_external_repository_object(
                $this->exportValues(), 
                $_FILES[self::FILE]);
        }
        else
        {
            return false;
        }
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
        
        $this->addElement('file', self::FILE, Translation::get('FileName'));
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation::get('Create'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation::get('Reset'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
