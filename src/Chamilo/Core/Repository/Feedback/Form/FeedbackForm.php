<?php
namespace Chamilo\Core\Repository\Feedback\Form;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the feedback
 */
class FeedbackForm extends FormValidator
{

    /**
     * Constructor
     *
     * @param string $form_url
     * @param Feedback $feedback
     */
    public function __construct($form_url, $feedback = null)
    {
        parent :: __construct('feedback', 'post', $form_url);

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
     * @param Schema $schema
     */
    protected function set_defaults($feedback)
    {
        $defaults = array();
        $defaults[Feedback :: PROPERTY_COMMENT] = $feedback->get_comment();
        $this->setDefaults($defaults);
    }
}