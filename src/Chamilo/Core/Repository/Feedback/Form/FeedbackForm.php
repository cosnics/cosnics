<?php

namespace Chamilo\Core\Repository\Feedback\Form;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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
        parent::__construct('feedback', self::FORM_METHOD_POST, $form_url);
        $this->application = $application;
        $this->build_form();

        if ($feedback && $feedback->isIdentified())
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

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Save', null, StringUtilities::LIBRARIES)
        );

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, StringUtilities::LIBRARIES)
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
        $defaults = [];
        if ($feedback && $feedback->isIdentified())
        {
            $defaults[Feedback::PROPERTY_COMMENT] = $feedback->get_comment();
        }

        $this->setDefaults($defaults);
    }
}