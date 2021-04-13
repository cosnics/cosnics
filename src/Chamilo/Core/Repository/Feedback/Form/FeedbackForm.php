<?php

namespace Chamilo\Core\Repository\Feedback\Form;

use Chamilo\Core\Repository\Feedback\PrivateFeedbackSupport;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
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
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    protected $supportsPrivateFeedback;

    /**
     * Constructor
     *
     * @param Application $application
     * @param ContentObjectRepository $contentObjectRepository
     * @param string $form_url
     * @param Feedback $feedback
     *
     * @throws \Exception
     */
    public function __construct(
        Application $application, ContentObjectRepository $contentObjectRepository, $form_url, $feedback = null, $supportsPrivateFeedback = false
    )
    {
        parent::__construct('feedback', 'post', $form_url);

        $this->application = $application;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->supportsPrivateFeedback = $supportsPrivateFeedback;

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
            array('width' => '100%', 'collapse_toolbar' => true, 'height' => 100, 'render_resource_inline' => false)
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

        if ($this->supportsPrivateFeedback)
        {
            $this->addElement('checkbox', PrivateFeedbackSupport::PROPERTY_PRIVATE, Translation::get('IsPrivate'));
        }

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
        $renderer->setElementTemplate('<div class="form-group">{element}</div>', 'buttons');
        $renderer->setRequiredNoteTemplate(null);
    }

    /**
     * Sets the default values
     *
     * @param Feedback|null $feedback
     *
     * @throws \Exception
     */
    protected function set_defaults(Feedback $feedback = null)
    {
        $defaults = array();
        if ($feedback instanceof Feedback && $feedback->is_identified())
        {
            if ($feedback->getFeedbackContentObjectId() > 0)
            {
                $feedbackContentObject =
                    $this->contentObjectRepository->findById($feedback->getFeedbackContentObjectId());
                if (!$feedbackContentObject instanceof
                    \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback)
                {
                    throw new \RuntimeException(
                        sprintf(
                            'The given feedback with id %s references an invalid content object', $feedback->getId()
                        )
                    );
                }

                $defaults[Feedback::PROPERTY_COMMENT] = $feedbackContentObject->get_description();
            }
            else
            {
                $defaults[Feedback::PROPERTY_COMMENT] = $feedback->get_comment();
            }

            if ($this->supportsPrivateFeedback && $feedback instanceof PrivateFeedbackSupport)
            {
                $defaults[PrivateFeedbackSupport::PROPERTY_PRIVATE] = $feedback->isPrivate();
            }
        }

        $this->setDefaults($defaults);
    }
}
