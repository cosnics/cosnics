<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Form;

use Chamilo\Core\Repository\Implementation\Matterhorn\ExternalObject;
use Chamilo\Core\Repository\Implementation\Matterhorn\Series;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalObjectForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const PARAM_UPLOAD = 'upload';
    const PARAM_WORKFLOW = 'workflow';
    const PARAM_DISTRIBUTE = 'distribute';
    const VIDEO_TITLE = 'title';
    const VIDEO_DESCRIPTION = 'description';
    const NEW_SERIES = 'new_series';

    private $application;

    private $form_type;

    private $external_repository_object;

    public function __construct($form_type, $action, $application)
    {
        parent::__construct('matterhorn_upload', 'post', $action);
        
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

    public function set_external_repository_object($object)
    {
        $this->external_repository_object = $object;
        
        $defaults[ExternalObject::PROPERTY_ID] = $object->get_id();
        $defaults[ExternalObject::PROPERTY_TITLE] = $object->get_title();
        $defaults[ExternalObject::PROPERTY_DESCRIPTION] = $object->get_description();
        $defaults[ExternalObject::PROPERTY_DURATION] = $object->get_duration();
        $defaults[ExternalObject::PROPERTY_CONTRIBUTORS] = $object->get_contributors();
        $defaults[ExternalObject::PROPERTY_SERIES] = $object->get_series();
        $defaults[ExternalObject::PROPERTY_OWNER_ID] = $object->get_owner_id();
        $defaults[ExternalObject::PROPERTY_CREATED] = $object->get_created();
        $defaults[ExternalObject::PROPERTY_SUBJECTS] = $object->get_subjects();
        $defaults[ExternalObject::PROPERTY_LICENSE] = $object->get_license();
        $defaults[ExternalObject::PROPERTY_TYPE] = $object->get_type();
        $defaults[ExternalObject::PROPERTY_MODIFIED] = $object->get_modified();
        $defaults[ExternalObject::PROPERTY_TRACKS] = $object->get_tracks();
        $defaults[ExternalObject::PROPERTY_ATTACHMENTS] = $object->get_attachments();
        
        parent::setDefaults($defaults);
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
            'text', 
            ExternalObject::PROPERTY_CONTRIBUTORS, 
            Translation::get('Contributors'), 
            array("size" => "50"));
        
        $this->addElement('text', ExternalObject::PROPERTY_OWNER_ID, Translation::get('Creator'), array("size" => "50"));
        
        $this->addElement(
            'text', 
            ExternalObject::PROPERTY_SUBJECTS, 
            Translation::get('Subjects'), 
            array("size" => "50"));
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
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
        
        $this->addElement('select', Series::PROPERTY_TITLE, Translation::get('Series'), $this->get_all_series());
        
        $this->addRule(
            Series::PROPERTY_TITLE, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'textarea', 
            ExternalObject::PROPERTY_DESCRIPTION, 
            Translation::get('Description', null, Utilities::COMMON_LIBRARIES), 
            array("rows" => "7", "cols" => "80"));
        
        $this->addElement('text', ExternalObject::PROPERTY_LICENSE, Translation::get('License'), array("size" => "50"));
        
        // series
        $series_group = array();
        $series_group[] = $this->createElement('select', 'series', Translation::get('Series'), $this->get_series_list());
        $series_group[] = $this->createElement(
            'image', 
            'add_series', 
            Theme::getInstance()->getCommonImagePath('Action/Add'), 
            array('id' => 'add_series', 'style' => 'display:none'));
        $this->addGroup($series_group, null, Translation::get('Series'));
        
        $group = array();
        $group[] = $this->createElement('static', null, null, '<div id="' . self::NEW_SERIES . '">');
        $group[] = $this->createElement('static', null, null, Translation::get('AddNewSeries'));
        $group[] = $this->createElement('text', self::NEW_SERIES);
        $group[] = $this->createElement('static', null, null, '</div>');
        $this->addGroup($group);
        
        // file upload choice
        $this->addElement(
            'radio', 
            self::PARAM_UPLOAD, 
            Translation::get('File'), 
            Translation::get('FileUpload'), 
            0, 
            array('id' => 'file_upload'));
        
        $this->addElement('html', '<div style="margin-left:25px;display:block;" id="upload">');
        
        $this->addElement('file', 'track');
        $this->addElement('html', '</div>');
        
        $this->addElement('radio', self::PARAM_UPLOAD, '', Translation::get('Inbox'), 1, array('id' => 'file_inbox'));
        
        $this->addElement('html', '<div style="margin-left:25px;display:block;" id="inbox">');
        
        $this->addElement('select', 'inbox', '', $this->get_inbox_list());
        $this->addElement('html', '</div>');
        
        // workflow choice
        $this->addElement(
            'radio', 
            self::PARAM_WORKFLOW, 
            Translation::get('Type'), 
            Translation::get('Audio'), 
            'audio/source');
        
        $this->addElement('radio', self::PARAM_WORKFLOW, null, Translation::get('Video'), 'video/source');
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Matterhorn', true) .
                     'Upload.js'));
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        $defaults[self::PARAM_UPLOAD] = 0;
        $defaults[self::PARAM_WORKFLOW] = 'audio/source';
        parent::setDefaults($defaults);
    }

    public function get_workflows_list()
    {
        $workflows = $this->application->get_external_repository_manager_connector()->get_workflows_list();
        $options = array();
        while ($workflow = $workflows->next_result())
        {
            $options[$workflow->get_id()] = $workflow->get_title();
        }
        return $options;
    }

    public function get_series_list()
    {
        $series = $this->application->get_external_repository_manager_connector()->get_all_series();
        $options = array();
        $options[0] = Translation::get('NoSeries');
        while ($serie = $series->next_result())
        {
            $options[$serie->get_id()] = $serie->get_title();
        }
        return $options;
    }

    public function get_category_list()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->application->get_user_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    public function get_inbox_list()
    {
        $files = $this->application->get_external_repository_manager_connector()->get_inbox_list();
        $options = array();
        while ($file = $files->next_result())
        {
            $options[$file->get_path()] = $file->get_id();
        }
        return $options;
    }

    public function get_file_upload()
    {
        $this->addElement('file', 'track');
    }

    public function update_video_entry()
    {
        $matterhorn = $this->application->get_external_repository_manager_connector();
        $values = $this->exportValues();
        return $matterhorn->update_matterhorn_video($values);
    }

    public function upload_video()
    {
        $values = $this->exportValues();
        if ($values[ExternalObjectForm::PARAM_UPLOAD] == 0)
        {
            if (StringUtilities::getInstance()->hasValue(($_FILES['track']['name'])))
            {
                $folder = Path::getInstance()->getTemporaryPath(__NAMESPACE__) . $this->application->get_user_id() . '/';
                if (! file_exists($folder) || ! is_dir($folder))
                {
                    Filesystem::create_dir($folder);
                }
                $path = $folder . $_FILES['track']['name'];
                if (Filesystem::move_file($_FILES['track']['tmp_name'], $path))
                {
                    $object = $this->application->get_external_repository_manager_connector()->create_external_repository_object(
                        $values, 
                        $path);
                    Filesystem::remove($path);
                    if ($object)
                    {
                        return $object->get_id();
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        elseif ($values[ExternalObjectForm::PARAM_UPLOAD] == 1)
        {
            $object = $this->application->get_external_repository_manager_connector()->create_external_repository_object(
                $values);
            return $object->get_id();
        }
    }

    public function get_all_series()
    {
        $series = $this->application->get_external_repository_manager_connector()->get_all_series();
        
        $seriesList = array();
        while ($opencast_series = $series->next_result())
        {
            $seriesList[$opencast_series->get_id()] = $opencast_series->get_title();
        }
        return $seriesList;
    }
}
