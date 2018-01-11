<?php

namespace Chamilo\Core\Repository\Feedback\Form;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the feedback
 */
class FeedbackForm extends FormValidator
{
    const PROPERTY_NOTIFICATIONS = 'notifications';
    const PROPERTY_ATTACHMENTS = 'attachments';
    const PROPERTY_ATTACHMENTS_UPLOADER = 'attachments_uploader';

    private $application;

    /**
     * Constructor
     *
     * @param string $form_url
     * @param Feedback $feedback
     */
    public function __construct(Application $application, $form_url, $feedback = null)
    {
        parent::__construct('feedback', 'post', $form_url);
        $this->application = $application;
        $this->build_form();

        if ($feedback && $feedback->is_identified())
        {
            $this->set_defaults($feedback);
        }
    }

    /**
     * Builds this form
     */
    protected function build_form()
    {
        $renderer = $this->get_renderer();

        $this->add_html_editor(
            Feedback::PROPERTY_COMMENT,
            Translation::get('AddFeedback'),
            true,
            array('width' => '100%', 'collapse_toolbar' => true, 'height' => 100)
        );

        $renderer->setElementTemplate('<div class="form-group">{element}</div>', Feedback::PROPERTY_COMMENT);

//        $calculator = new Calculator(
//            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
//                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
//                (int) $this->application->getUser()->getId()
//            )
//        );
//
//        $uploadUrl = new Redirect(
//            array(
//                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::context(),
//                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE
//            )
//        );
//
//        $dropZoneParameters = array(
//            'name' => self::PROPERTY_ATTACHMENTS_UPLOADER,
//            'maxFilesize' => $calculator->getMaximumUploadSize(),
//            'uploadUrl' => $uploadUrl->getUrl(),
//            'successCallbackFunction' => 'chamilo.core.repository.feedback.importAttachment.processUploadedFile',
//            'sendingCallbackFunction' => 'chamilo.core.repository.feedback.importAttachment.prepareRequest',
//            'removedfileCallbackFunction' => 'chamilo.core.repository.feedback.importAttachment.deleteUploadedFile'
//        );
//
//        $this->addFileDropzone(self::PROPERTY_ATTACHMENTS_UPLOADER, $dropZoneParameters, false);
//
//        $this->addElement(
//            'html',
//            ResourceManager::getInstance()->get_resource_html(
//                Path::getInstance()->getJavascriptPath(\Chamilo\Core\Repository\Manager::context(), true) .
//                'Plugin/jquery.file.upload.import.js'
//            )
//        );
//
//        $this->addElement(
//            'html',
//            ResourceManager::getInstance()->get_resource_html(
//                Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'FeedbackAttachmentsUpload.js'
//            )
//        );
//
//        $this->addElement('hidden', self::PROPERTY_ATTACHMENTS);
//
//        $renderer->setElementTemplate(
//            '<div class="form-group">{element}</div>', self::PROPERTY_ATTACHMENTS_UPLOADER . '_static_data'
//        );

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES)
        );

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $renderer->setElementTemplate('<div class="form-group">{element}</div>', 'buttons');
        $renderer->setRequiredNoteTemplate(null);
    }

    /**
     * Sets the default values
     *
     * @param Schema $schema
     */
    protected function set_defaults($feedback)
    {
        $defaults = array();
        if ($feedback && $feedback->is_identified())
        {
            $defaults[Feedback::PROPERTY_COMMENT] = $feedback->get_comment();
        }

        $this->setDefaults($defaults);
    }
}