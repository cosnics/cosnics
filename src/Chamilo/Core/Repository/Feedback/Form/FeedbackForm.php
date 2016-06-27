<?php
namespace Chamilo\Core\Repository\Feedback\Form;

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
        $renderer = $this->get_renderer();

        $this->add_html_editor(
            Feedback :: PROPERTY_COMMENT,
            Translation :: get('AddFeedback'),
            true,
            array('width' => '100%', 'collapse_toolbar' => true));

        $renderer->setElementTemplate('<div class="form-group">{element}</div>', Feedback :: PROPERTY_COMMENT);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES));

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $renderer->setElementTemplate('<div class="form-group">{element}</div>', 'buttons');
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

        $this->setDefaults($defaults);
    }
}