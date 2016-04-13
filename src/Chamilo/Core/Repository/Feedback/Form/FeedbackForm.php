<?php
namespace Chamilo\Core\Repository\Feedback\Form;

use Chamilo\Application\Portfolio\Storage\DataClass\Notification;
use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the feedback
 */
class FeedbackForm extends FormValidator
{
    const PROPERTY_NOTIFICATIONS = 'notifications';

    private $application;

    /**
     * Constructor
     *
     * @param string $form_url
     * @param Feedback $feedback
     */
    public function __construct(Application $application, $form_url, $feedback = null)
    {
        parent :: __construct('feedback', 'post', $form_url);
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
        $this->add_html_editor(Feedback :: PROPERTY_COMMENT, Translation :: get('AddFeedback'), true);

        if ($this->application->get_parent() instanceof FeedbackNotificationSupport)
        {
            if ($this->application->get_parent()->is_allowed_to_view_feedback())
            {
                $this->addElement(
                    'checkbox',
                    self :: PROPERTY_NOTIFICATIONS,
                    Translation :: get('ReceiveNotifications'));
            }
        }
        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES));

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
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
            $defaults[Feedback :: PROPERTY_COMMENT] = $feedback->get_comment();
        }
        if ($this->application->get_parent() instanceof FeedbackNotificationSupport)
        {

            if ($this->application->get_parent()->is_allowed_to_view_feedback())
            {
                $notification = $this->application->get_parent()->retrieve_notification();

                if ($notification instanceof Notification)
                {
                    $defaults[self :: PROPERTY_NOTIFICATIONS] = 1;
                }
            }
        }

        $this->setDefaults($defaults);
    }
}