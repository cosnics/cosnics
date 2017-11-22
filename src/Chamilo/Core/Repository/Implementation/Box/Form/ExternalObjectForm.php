<?php
namespace Chamilo\Core\Repository\Implementation\Box\Form;

use Chamilo\Core\Repository\Implementation\Box\ExternalObject;
use Chamilo\Core\Repository\Implementation\Box\ExternalObjectDisplay;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalObjectForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_NEW_FOLDER = 3;
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
        elseif ($this->form_type == self::TYPE_NEW_FOLDER)
        {
            $this->build_newfolder_form();
        }
        
        $this->setDefaults();
    }

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;
        
        $defaults[ExternalObject::PROPERTY_ID] = $external_repository_object->get_id();
        
        $display = ExternalObjectDisplay::factory($external_repository_object);
        $defaults[self::PREVIEW] = $display->get_preview();
        
        parent::setDefaults($defaults);
    }

    public function build_basic_form()
    {
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->add_information_message('box_api_move', null, Translation::get('BoxAPIMoveImpossible'));
        }
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

    public function update_file()
    {
        return $this->application->get_external_repository_manager_connector()->update_external_repository_object(
            $this->exportValues());
    }

    public function upload_file()
    {
        if (StringUtilities::getInstance()->hasValue(($_FILES[self::FILE]['name'])))
        {
            if ($this->application->get_external_repository_manager_connector()->create_external_repository_object(
                $_FILES[self::FILE]))
                return true;
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
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_newfolder_form()
    {
        $this->addElement('text', 'foldername', 'Name of new folder', array('size' => '50'));
        $this->addRule(
            'foldername', 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('hidden', 'folder');
        $this->setDefaults(array('folder' => Request::get('folder')));
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function create_folder($folder)
    {
        if (empty($_POST['folder']))
        {
            $folder = 0;
        }
        else
            $folder = $_POST['folder'];
        
        if (! is_null($_POST['foldername']))
        {
            return $this->application->get_external_repository_manager_connector()->create_external_repository_folder(
                $_POST['foldername'], 
                $folder);
        }
        else
            return null;
    }
}
