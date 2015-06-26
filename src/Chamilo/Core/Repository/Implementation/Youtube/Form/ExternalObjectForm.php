<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Form;

use Chamilo\Core\Repository\Implementation\Youtube\DataConnector;
use Chamilo\Core\Repository\Implementation\Youtube\ExternalObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: youtube_external_repository_manager_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 *
 * @package
 *
 *
 *
 */
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

    private $application;

    private $form_type;

    private $video;

    private $external_repository_object;

    public function __construct($form_type, $action, $application)
    {
        parent :: __construct('youtube_upload', 'post', $action);

        $this->application = $application;

        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_uploading_form();
        }
        elseif ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_edit_form();
        }

        $this->setDefaults();
    }

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;
        $this->addElement('hidden', ExternalObject :: PROPERTY_ID);
        $defaults[ExternalObject :: PROPERTY_TITLE] = $external_repository_object->get_title();
        $defaults[ExternalObject :: PROPERTY_DESCRIPTION] = $external_repository_object->get_description();
        $defaults[ExternalObject :: PROPERTY_CATEGORY] = $external_repository_object->get_category();
        $defaults[ExternalObject :: PROPERTY_TAGS] = $this->get_tags();

        parent :: setDefaults($defaults);
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
            ExternalObject :: PROPERTY_TITLE,
            Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES),
            array("size" => "50"));
        $this->addRule(
            ExternalObject :: PROPERTY_TITLE,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'select',
            ExternalObject :: PROPERTY_CATEGORY,
            Translation :: get('Category', null, Utilities :: COMMON_LIBRARIES),
            $this->get_youtube_categories());

        $this->addElement(
            'textarea',
            ExternalObject :: PROPERTY_TAGS,
            Translation :: get('Tags'),
            array("rows" => "2", "cols" => "80"));
        $this->addRule(
            ExternalObject :: PROPERTY_TAGS,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'textarea',
            ExternalObject :: PROPERTY_DESCRIPTION,
            Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES),
            array("rows" => "7", "cols" => "80"));
    }

    public function build_upload_form()
    {
        $this->addElement('file', ExternalObject :: PROPERTY_FILE, sprintf(Translation :: get('FileName'), '2Gb'));

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Upload', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function get_youtube_categories()
    {
        $youtube = $this->application->get_external_repository_manager_connector();
        return $youtube->retrieve_categories();
    }

    public function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', ExternalObject :: PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_uploading_form()
    {
        $this->build_basic_form();

        // $this->addElement('hidden', 'token', $this->token);
        $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), '2Gb'));

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Upload', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_video_entry()
    {
        $youtube = $this->application->get_external_repository_manager_connector();
        $values = $this->exportValues();

        return $youtube->update_youtube_video($values);
    }

    public function upload_video()
    {
        $values = $this->exportValues();
        $connector = $this->application->get_external_repository_manager_connector();
        return $connector->upload_video($values, $_FILES['file']);
    }

    /**
     * Sets default values.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        // $defaults[self :: VIDEO_TITLE] = $this->video->getVideoTitle();
        // $defaults[self :: VIDEO_CATEGORY] = $this->video_entry->getVideoCategory();
        // $defaults[self :: VIDEO_TAGS] = $this->video_entry->getVideoTags();
        // $defaults[self :: VIDEO_DESCRIPTION] = $this->video_entry->getVideoDescription();
        parent :: setDefaults($defaults);
    }

    public function get_video()
    {
        return $this->video;
    }
}
