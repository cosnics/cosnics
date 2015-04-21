<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Form;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\content_object\assessment\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigurationForm extends FormValidator
{
    const PROPERTY_ANSWER_FEEDBACK_OPTION = 'answer_feedback_option';

    /**
     *
     * @param Configuration $configuration
     * @param string $action
     */
    public function __construct(Configuration $configuration, $action)
    {
        parent :: __construct('configuration', 'post', $action);

        $this->build_form();

        self :: defaults($this, $configuration);
    }

    public function build_form()
    {
        self :: build($this);

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     *
     * @param ConfigurationForm $form
     */
    static public function build(ConfigurationForm $form)
    {
        // Hinting
        $form->addElement('category', Translation :: get('Hinting'));
        $form->addElement('checkbox', Configuration :: PROPERTY_ALLOW_HINTS, Translation :: get('AllowHints'));
        $form->addElement('category');

        // Feedback
        $form->addElement('category', Translation :: get('Feedback'));
        $form->addElement(
            'checkbox',
            Configuration :: PROPERTY_SHOW_SCORE,
            Translation :: get('ShowScores'),
            Translation :: get('ShowScoresDetail'));

        $form->addElement(
            'checkbox',
            Configuration :: PROPERTY_SHOW_CORRECTION,
            Translation :: get('ShowCorrection'),
            Translation :: get('ShowCorrectionDetail'));

        $form->addElement(
            'checkbox',
            Configuration :: PROPERTY_SHOW_SOLUTION,
            Translation :: get('ShowSolution'),
            Translation :: get('ShowSolutionDetail'));

        $form->addElement(
            self :: build_answer_feedback(
                $form,
                array(
                    Configuration :: ANSWER_FEEDBACK_TYPE_QUESTION,
                    Configuration :: ANSWER_FEEDBACK_TYPE_GIVEN,
                    Configuration :: ANSWER_FEEDBACK_TYPE_GIVEN_CORRECT,
                    Configuration :: ANSWER_FEEDBACK_TYPE_GIVEN_WRONG,
                    Configuration :: ANSWER_FEEDBACK_TYPE_CORRECT,
                    Configuration :: ANSWER_FEEDBACK_TYPE_WRONG,
                    Configuration :: ANSWER_FEEDBACK_TYPE_ALL)));

        $feedback_locations = array();
        $feedback_locations[Configuration :: FEEDBACK_LOCATION_TYPE_PAGE] = Translation :: get('FeedbackAfterEveryPage');
        $feedback_locations[Configuration :: FEEDBACK_LOCATION_TYPE_SUMMARY] = Translation :: get('FeedbackAtTheEnd');
        $feedback_locations[Configuration :: FEEDBACK_LOCATION_TYPE_BOTH] = Translation :: get(
            'FeedbackAfterEveryPageAndAtTheEnd');

        $form->addElement(
            'select',
            Configuration :: PROPERTY_FEEDBACK_LOCATION,
            Translation :: get('FeedbackLocation'),
            $feedback_locations);

        $form->addElement('category');

        $form->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                    true) . 'FeedbackForm.js'));
    }

    /**
     *
     * @param FormValidator $form
     * @param int[] $answer_feedback_types
     * @return HTML_QuickForm_Element[]
     */
    static function build_answer_feedback(FormValidator $form, $answer_feedback_types = array())
    {
        $answer_feedback_fields = array();

        $answer_feedback_fields[] = $form->createElement('checkbox', self :: PROPERTY_ANSWER_FEEDBACK_OPTION);

        $answer_feedback_options = array();

        foreach ($answer_feedback_types as $answer_feedback_type)
        {
            $answer_feedback_options[$answer_feedback_type] = Configuration :: answer_feedback_string(
                $answer_feedback_type);
        }

        $answer_feedback_fields[] = $form->createElement('static', null, null, '<span id="answer_feedback_enabled">');
        $answer_feedback_fields[] = $form->createElement(
            'static',
            null,
            null,
            Translation :: get('ShowAnswerFeedbackDetail'));

        $answer_feedback_fields[] = $form->createElement('static', null, null, '&nbsp;');

        $answer_feedback_fields[] = $form->createElement(
            'select',
            Configuration :: PROPERTY_SHOW_ANSWER_FEEDBACK,
            null,
            $answer_feedback_options);

        $answer_feedback_fields[] = $form->createElement('static', null, null, '</span>');

        return $form->createGroup(
            $answer_feedback_fields,
            'answer_feedback_fields',
            Translation :: get('ShowAnswerFeedback'),
            '',
            false);
    }

    /**
     *
     * @param ConfigurationForm $form
     * @param Configuration $configuration
     */
    static public function defaults(ConfigurationForm $form, Configuration $configuration)
    {
        $defaults[Configuration :: PROPERTY_ALLOW_HINTS] = $configuration->get_allow_hints();
        $defaults[Configuration :: PROPERTY_SHOW_SCORE] = $configuration->get_show_score();
        $defaults[Configuration :: PROPERTY_SHOW_CORRECTION] = $configuration->get_show_correction();
        $defaults[Configuration :: PROPERTY_SHOW_SOLUTION] = $configuration->get_show_solution();

        if ($configuration->get_show_answer_feedback() == Configuration :: ANSWER_FEEDBACK_TYPE_NONE)
        {
            $defaults[self :: PROPERTY_ANSWER_FEEDBACK_OPTION] = 0;
        }
        else
        {
            $defaults[self :: PROPERTY_ANSWER_FEEDBACK_OPTION] = 1;
            $defaults[Configuration :: PROPERTY_SHOW_ANSWER_FEEDBACK] = $configuration->get_show_answer_feedback();
        }

        $defaults[Configuration :: PROPERTY_FEEDBACK_LOCATION] = $configuration->get_feedback_location();

        $form->setDefaults($defaults);
    }
}
