<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Form;

use Chamilo\Core\Repository\Implementation\GoogleDocs\ExternalObject;
use Chamilo\Core\Repository\Implementation\GoogleDocs\ExternalObjectDisplay;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalObjectForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_NEWFOLDER = 3;
    const PREVIEW = 'preview';
    const FILE = 'file';
    const PARENT_ID = 'parent_id';
    const NEW_FOLDER = 'new_folder';

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
        elseif ($this->form_type == self :: TYPE_NEWFOLDER)
        {
            $this->build_newfolder_form();
        }

        $this->setDefaults();
    }

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;

        $defaults[ExternalObject :: PROPERTY_ID] = $external_repository_object->get_id();

        $display = ExternalObjectDisplay :: factory($external_repository_object);
        $defaults[self :: PREVIEW] = $display->get_preview();

        parent :: setDefaults($defaults);
    }

    public function build_basic_form()
    {
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->add_information_message(
                'google_docs_api_move',
                null,
                Translation :: get('GoogleDocsAPIMoveImpossible'));
        }
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

    public function update_file()
    {
        return $this->application->get_external_repository_manager_connector()->update_external_repository_object(
            $this->exportValues());
    }

    public function upload_file($folder)
    {
        if (StringUtilities :: getInstance()->hasValue(($_FILES[self :: FILE])))
        {
            return $this->application->get_external_repository_manager_connector()->create_external_repository_object(
                $_FILES[self :: FILE],
                $folder);
        }
        else
        {
            return null;
        }
    }

    public function get_folders()
    {
        $folders = $this->application->get_external_repository_manager_connector()->retrieve_my_folders('root');
        $new_folders = array();
        while ($folder = $folders->next_result())
        {
            $new_folders[$folder->getId()] = $folder->getTitle();
        }
        return $new_folders;
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $category_group = array();
        $category_group[] = $this->createElement(
            'select',
            self :: PARENT_ID,
            Translation :: get('FolderTypeName'),
            $this->get_folders());
        $category_group[] = $this->createElement(
            'image',
            'add_folder',
            Theme :: getInstance()->getCommonImagePath('Action/Add'),
            array('id' => 'add_folder', 'style' => 'display:none'));
        $this->addGroup($category_group, null, Translation :: get('FolderTypeName'));

        $group = array();
        $group[] = $this->createElement('static', null, null, '<div id="' . self :: NEW_FOLDER . '">');
        $group[] = $this->createElement('text', self :: NEW_FOLDER);
        $group[] = $this->createElement('static', null, null, '</div>');
        $this->addGroup($group);

        $this->addElement('file', self :: FILE, Translation :: get('FileName'));

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement(
            'html',
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\GoogleDocs', true) .
                     'Upload.js'));
    }

    public function build_newfolder_form()
    {
        $this->addElement('text', 'foldername', 'Name of new folder', array('size' => '50'));
        $this->addRule(
            'foldername',
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement('hidden', 'folder');
        $this->setDefaults(array('folder' => Request :: get('folder')));

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function create_folder($folder)
    {
        if (! is_null($_POST['foldername']))
        {
            return $this->application->get_external_repository_manager_connector()->create_external_repository_folder(
                $_POST['foldername'],
                $_POST['folder']);
        }
        else
            return null;
    }
}
