<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Form;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: assessment_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.assessment
 */
/**
 * This class represents a form to create or update assessment
 */
class AssessmentForm extends ContentObjectForm
{
    const UNLIMITED_ATTEMPTS = 'unlimited_attempts';
    const LIMITED_ATTEMPTS = 'limited_attempts';
    const ALL_QUESTIONS = 'all_questions';
    const UNLIMITED_TIME = 'unlimited_time';
    const RANDOM_QUESTIONS = 'random';
    const SESSION_QUESTIONS = 'questions';

    public function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS] = $object->get_maximum_attempts();
            $defaults[self :: UNLIMITED_ATTEMPTS] = ($defaults[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS] > 0 ? 1 : 0);
            $defaults[Assessment :: PROPERTY_QUESTIONS_PER_PAGE] = $object->get_questions_per_page();
            $defaults[self :: ALL_QUESTIONS] = ($defaults[Assessment :: PROPERTY_QUESTIONS_PER_PAGE] > 0 ? 1 : 0);
            $defaults[Assessment :: PROPERTY_MAXIMUM_TIME] = $object->get_maximum_time();
            $defaults[self :: UNLIMITED_TIME] = ($defaults[Assessment :: PROPERTY_MAXIMUM_TIME] > 0 ? 1 : 0);
            $defaults[Assessment :: PROPERTY_RANDOM_QUESTIONS] = $object->get_random_questions();
            $defaults[self :: RANDOM_QUESTIONS] = ($defaults[Assessment :: PROPERTY_RANDOM_QUESTIONS] > 0 ? 1 : 0);
        }
        else
        {
            $defaults[Assessment :: PROPERTY_ASSESSMENT_TYPE] = 0;
            $defaults[self :: UNLIMITED_ATTEMPTS] = 0;
            $defaults[self :: ALL_QUESTIONS] = 0;
            $defaults[self :: UNLIMITED_TIME] = 0;
            $defaults[self :: RANDOM_QUESTIONS] = 0;
        }

        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        // Number of attempts
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_ATTEMPTS,
            '',
            Translation :: get('UnlimitedAttempts'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: UNLIMITED_ATTEMPTS . '_window\')',
                'id' => self :: UNLIMITED_ATTEMPTS));
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_ATTEMPTS,
            '',
            Translation :: get('LimitedAttempts'),
            1,
            array(
                'onclick' => 'javascript:window_show(\'' . self :: UNLIMITED_ATTEMPTS . '_window\')',
                'id' => self :: LIMITED_ATTEMPTS));
        $this->addGroup($choices, null, Translation :: get('MaximumAttempts'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: UNLIMITED_ATTEMPTS . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, null, false, array('id' => 'attempts'));
        $this->addElement('html', '</div>');

        // Number of questions per page
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: ALL_QUESTIONS,
            '',
            Translation :: get('AllQuestionsOnOnePage'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: ALL_QUESTIONS . '_window\')',
                'id' => self :: ALL_QUESTIONS));
        $choices[] = $this->createElement(
            'radio',
            self :: ALL_QUESTIONS,
            '',
            Translation :: get('LimitedQuestionsOnOnePage'),
            1,
            array('onclick' => 'javascript:window_show(\'' . self :: ALL_QUESTIONS . '_window\')'));
        $this->addGroup($choices, null, Translation :: get('QuestionsPerPage'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: ALL_QUESTIONS . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, null, false, array('id' => 'questions'));
        $this->addElement('html', '</div>');

        // Maximum time allowed
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_TIME,
            '',
            Translation :: get('Unlimited'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: UNLIMITED_TIME . '_window\')',
                'id' => self :: UNLIMITED_TIME));
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_TIME,
            '',
            Translation :: get('Limited'),
            1,
            array('onclick' => 'javascript:window_show(\'' . self :: UNLIMITED_TIME . '_window\')'));
        $this->addGroup($choices, null, Translation :: get('MaximumTimeAllowedMinutes'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: UNLIMITED_TIME . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_MAXIMUM_TIME, null, false, array('id' => 'time'));
        $this->addElement('html', '</div>');

        // Random questions
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: RANDOM_QUESTIONS,
            '',
            Translation :: get('NoRandomization'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: RANDOM_QUESTIONS . '_window\')',
                'id' => self :: RANDOM_QUESTIONS));
        $choices[] = $this->createElement(
            'radio',
            self :: RANDOM_QUESTIONS,
            '',
            Translation :: get('RandomQuestions'),
            1,
            array('onclick' => 'javascript:window_show(\'' . self :: RANDOM_QUESTIONS . '_window\')'));
        $this->addGroup($choices, null, Translation :: get('AmountOfRandomQuestions'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: RANDOM_QUESTIONS . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_RANDOM_QUESTIONS, null, false, array('id' => 'number_random'));
        $this->addElement('html', '</div>');

        $this->addElement('category');

        $this->addElement(
            'html',
            "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var " . self :: UNLIMITED_ATTEMPTS . " = document.getElementById('" . self :: UNLIMITED_ATTEMPTS . "');
					if (" . self :: UNLIMITED_ATTEMPTS . ".checked)
					{
						window_hide('" . self :: UNLIMITED_ATTEMPTS . "_window');
					}

					var " . self :: ALL_QUESTIONS . " = document.getElementById('" . self :: ALL_QUESTIONS . "');
					if (" . self :: ALL_QUESTIONS . ".checked)
					{
						window_hide('" . self :: ALL_QUESTIONS . "_window');
					}

					var " . self :: UNLIMITED_TIME . " = document.getElementById('" . self :: UNLIMITED_TIME . "');
					if (" . self :: UNLIMITED_TIME . ".checked)
					{
						window_hide('" . self :: UNLIMITED_TIME . "_window');
					}

					var " . self :: RANDOM_QUESTIONS . " = document.getElementById('" . self :: RANDOM_QUESTIONS . "');
					if (" . self :: RANDOM_QUESTIONS . ".checked)
					{
						window_hide('" . self :: RANDOM_QUESTIONS . "_window');
					}

					function window_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function window_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");

        $this->addRule(
            Assessment :: PROPERTY_MAXIMUM_ATTEMPTS,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');
        $this->addRule(
            Assessment :: PROPERTY_QUESTIONS_PER_PAGE,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');
        $this->addRule(
            Assessment :: PROPERTY_MAXIMUM_TIME,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');
        $this->addRule(
            Assessment :: PROPERTY_RANDOM_QUESTIONS,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\Assessment', true) .
                     'AssessmentForm.js'));
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));

        // Number of attempts
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_ATTEMPTS,
            '',
            Translation :: get('UnlimitedAttempts'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: UNLIMITED_ATTEMPTS . '_window\')',
                'id' => self :: UNLIMITED_ATTEMPTS));
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_ATTEMPTS,
            '',
            Translation :: get('LimitedAttempts'),
            1,
            array(
                'onclick' => 'javascript:window_show(\'' . self :: UNLIMITED_ATTEMPTS . '_window\')',
                'id' => self :: LIMITED_ATTEMPTS));
        $this->addGroup($choices, null, Translation :: get('MaximumAttempts'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: UNLIMITED_ATTEMPTS . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, null, false, array('id' => 'attempts'));
        $this->addElement('html', '</div>');

        // Number of questions per page
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: ALL_QUESTIONS,
            '',
            Translation :: get('AllQuestionsOnOnePage'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: ALL_QUESTIONS . '_window\')',
                'id' => self :: ALL_QUESTIONS));
        $choices[] = $this->createElement(
            'radio',
            self :: ALL_QUESTIONS,
            '',
            Translation :: get('LimitedQuestionsOnOnePage'),
            1,
            array('onclick' => 'javascript:window_show(\'' . self :: ALL_QUESTIONS . '_window\')'));
        $this->addGroup($choices, null, Translation :: get('QuestionsPerPage'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: ALL_QUESTIONS . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_QUESTIONS_PER_PAGE, null, false, array('id' => 'questions'));
        $this->addElement('html', '</div>');

        // Maximum time allowed
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_TIME,
            '',
            Translation :: get('Unlimited'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: UNLIMITED_TIME . '_window\')',
                'id' => self :: UNLIMITED_TIME));
        $choices[] = $this->createElement(
            'radio',
            self :: UNLIMITED_TIME,
            '',
            Translation :: get('Limited'),
            1,
            array('onclick' => 'javascript:window_show(\'' . self :: UNLIMITED_TIME . '_window\')'));
        $this->addGroup($choices, null, Translation :: get('MaximumTimeAllowedMinutes'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: UNLIMITED_TIME . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_MAXIMUM_TIME, null, false, array('id' => 'time'));
        $this->addElement('html', '</div>');

        // Random questions
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: RANDOM_QUESTIONS,
            '',
            Translation :: get('NoRandomization'),
            0,
            array(
                'onclick' => 'javascript:window_hide(\'' . self :: RANDOM_QUESTIONS . '_window\')',
                'id' => self :: RANDOM_QUESTIONS));
        $choices[] = $this->createElement(
            'radio',
            self :: RANDOM_QUESTIONS,
            '',
            Translation :: get('RandomQuestions'),
            1,
            array('onclick' => 'javascript:window_show(\'' . self :: RANDOM_QUESTIONS . '_window\')'));
        $this->addGroup($choices, null, Translation :: get('AmountOfRandomQuestions'), '', false);
        $this->addElement(
            'html',
            '<div style="margin-left: 25px; display: block;" id="' . self :: RANDOM_QUESTIONS . '_window">');
        $this->add_textfield(Assessment :: PROPERTY_RANDOM_QUESTIONS, null, false, array('id' => 'number_random'));
        $this->addElement('html', '</div>');

        $this->addElement('category');

        $this->addElement(
            'html',
            "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var " . self :: UNLIMITED_ATTEMPTS . " = document.getElementById('" . self :: UNLIMITED_ATTEMPTS . "');
					if (" . self :: UNLIMITED_ATTEMPTS . ".checked)
					{
						window_hide('" . self :: UNLIMITED_ATTEMPTS . "_window');
					}

					var " . self :: ALL_QUESTIONS . " = document.getElementById('" . self :: ALL_QUESTIONS . "');
					if (" . self :: ALL_QUESTIONS . ".checked)
					{
						window_hide('" . self :: ALL_QUESTIONS . "_window');
					}

					var " . self :: UNLIMITED_TIME . " = document.getElementById('" . self :: UNLIMITED_TIME . "');
					if (" . self :: UNLIMITED_TIME . ".checked)
					{
						window_hide('" . self :: UNLIMITED_TIME . "_window');
					}

					var " . self :: RANDOM_QUESTIONS . " = document.getElementById('" . self :: RANDOM_QUESTIONS . "');
					if (" . self :: RANDOM_QUESTIONS . ".checked)
					{
						window_hide('" . self :: RANDOM_QUESTIONS . "_window');
					}

					function window_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function window_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");

        $this->addRule(
            Assessment :: PROPERTY_MAXIMUM_ATTEMPTS,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');
        $this->addRule(
            Assessment :: PROPERTY_QUESTIONS_PER_PAGE,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');
        $this->addRule(
            Assessment :: PROPERTY_MAXIMUM_TIME,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');
        $this->addRule(
            Assessment :: PROPERTY_RANDOM_QUESTIONS,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\Assessment', true) .
                     'AssessmentForm.js'));
    }

    // Inherited
    public function create_content_object()
    {
        $object = new Assessment();
        $values = $this->exportValues();
        $object->set_maximum_attempts($values[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS]);
        if ($object->get_maximum_attempts() == null)
            $object->set_maximum_attempts(0);

        $object->set_questions_per_page($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]);
        if ($object->get_questions_per_page() == null)
            $object->set_questions_per_page(0);

        $object->set_maximum_time($values[Assessment :: PROPERTY_MAXIMUM_TIME]);
        if ($object->get_maximum_time() == null)
            $object->set_maximum_time(0);

        $object->set_random_questions($values[Assessment :: PROPERTY_RANDOM_QUESTIONS]);
        if ($object->get_random_questions() == null)
            $object->set_random_questions(0);

        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();

        if ($values[self :: UNLIMITED_ATTEMPTS] == 0)
            $object->set_maximum_attempts(0);
        else
            $object->set_maximum_attempts($values[Assessment :: PROPERTY_MAXIMUM_ATTEMPTS]);

        if ($values[self :: ALL_QUESTIONS] == 0)
            $object->set_questions_per_page(0);
        else
            $object->set_questions_per_page($values[Assessment :: PROPERTY_QUESTIONS_PER_PAGE]);

        if ($values[self :: UNLIMITED_TIME] == 0)
            $object->set_maximum_time(0);
        else
            $object->set_maximum_time($values[Assessment :: PROPERTY_MAXIMUM_TIME]);

        if ($values[self :: RANDOM_QUESTIONS])
        {
            if (is_null($values[Assessment :: PROPERTY_RANDOM_QUESTIONS]) ||
                 ! is_numeric($values[Assessment :: PROPERTY_RANDOM_QUESTIONS]))
            {
                $object->set_random_questions(0);
            }
            else
            {
                // If the number of questions to be asked changes.
                if ($object->get_random_questions() != $values[Assessment :: PROPERTY_RANDOM_QUESTIONS])
                {
                    // Regenerate random questions.
                    Session :: unregister(self :: SESSION_QUESTIONS);
                }
                $object->set_random_questions($values[Assessment :: PROPERTY_RANDOM_QUESTIONS]);
            }
        }
        else
        {
            $object->set_random_questions(0);
        }

        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
