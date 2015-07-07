<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Form;

use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Storage\DataClass\AbstractFeedback;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Storage\DataClass\AbstractNotification;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feedback form
 *
 * @package repository\content_object\portfolio\feedback
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FeedbackForm extends FormValidator
{
    const PROPERTY_NOTIFICATIONS = 'notifications';

    /**
     *
     * @var \libraries\architecture\Application
     */
    private $application;

    /**
     * Constructor
     *
     * @param \libraries\architecture\Application $application
     * @param string $form_url
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     */
    public function __construct(Application $application, $form_url, $feedback = null)
    {
        parent :: __construct('feedback', 'post', $form_url);
        $this->application = $application;

        $this->build_form();
        $this->set_defaults($feedback);
    }

    /**
     * Builds this form
     */
    protected function build_form()
    {
        $this->add_html_editor(AbstractFeedback :: PROPERTY_COMMENT, Translation :: get('Comment'), true);

        if ($this->application->get_parent()->is_allowed_to_view_feedback())
        {
            $this->addElement(
                'checkbox',
                self :: PROPERTY_NOTIFICATIONS,
                Translation :: get('ReceiveNotifications'));
        }

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets the default values
     *
     * @param \core\repository\content_object\portfolio\feedback\Feedback $feedback
     */
    protected function set_defaults($feedback)
    {
        $defaults = array();

        if ($feedback && $feedback->is_identified())
        {
            $defaults[AbstractFeedback :: PROPERTY_COMMENT] = $feedback->get_comment();
        }

        if ($this->application->get_parent()->is_allowed_to_view_feedback())
        {
            $notification = $this->application->get_parent()->retrieve_notification();

            if ($notification instanceof AbstractNotification)
            {
                $defaults[self :: PROPERTY_NOTIFICATIONS] = 1;
            }
        }

        $this->setDefaults($defaults);
    }
}