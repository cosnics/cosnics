<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc
 */
abstract class QuestionDisplay
{

    private $complex_content_object_question;

    private $question;

    private $question_nr;

    private $formvalidator;

    private $renderer;

    public function __construct($formvalidator, $complex_content_object_question, $question_nr, $question)
    {
        $this->formvalidator = $formvalidator;
        $this->renderer = $formvalidator->defaultRenderer();

        $this->complex_content_object_question = $complex_content_object_question;
        $this->question_nr = $question_nr;
        $this->question = $question;
    }

    public function get_attempt()
    {
        return $this->get_formvalidator()->get_assessment_viewer()->get_assessment_question_attempt(
            $this->get_complex_content_object_question()->get_id());
    }

    public function get_configuration()
    {
        return $this->get_formvalidator()->get_assessment_viewer()->get_configuration();
    }

    public function get_answers()
    {
        $attempt = $this->get_attempt();
        if (! is_null($attempt))
        {
            return unserialize($attempt->get_answer());
        }
        else
        {
            return false;
        }
    }

    public function get_complex_content_object_question()
    {
        return $this->complex_content_object_question;
    }

    public function get_question()
    {
        return $this->question;
    }

    public function get_renderer()
    {
        return $this->renderer;
    }

    public function get_formvalidator()
    {
        return $this->formvalidator;
    }

    public function render()
    {
        $formvalidator = $this->formvalidator;
        $formvalidator->addElement(
            'hidden',
            'hint_question[' . $this->get_complex_content_object_question()->get_id() . ']',
            0);

        $this->add_header();
        $this->add_question_form();
        $this->add_footer();
    }

    abstract public function add_question_form();

    public function add_header()
    {
        $html = array();

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $this->question_nr . '. ' . $this->get_title() . '</h3>';
        $html[] = '</div>';

        $html[] = $this->get_description();

        $this->formvalidator->addElement('html', implode(PHP_EOL, $html));
    }

    public function get_title()
    {
        return $this->question->get_title();
    }

    public function get_description()
    {
        $html = array();

        if ($this->question->has_description())
        {
            $description = $this->question->get_description();
            $classes = $this->needsDescriptionBorder() ? 'panel-body panel-body-assessment-description' : 'panel-body';
            $renderer = new ContentObjectResourceRenderer($this, $description);

            $html[] = '<div class="' . $classes . '">';
            $html[] = $renderer->run();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function add_footer()
    {
        $formvalidator = $this->formvalidator;
        $html[] = '</div>';

        $footer = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $footer);
    }

    public function needsDescriptionBorder()
    {
        return false;
    }

    public static function factory($formvalidator, $complex_content_object_question, $question_nr)
    {
        $question = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $complex_content_object_question->get_ref());
        $type = $question->get_type();

        $class = ClassnameUtilities::getInstance()->getNamespaceParent($type, 3) . '\Integration\\' .
             Assessment::package() . '\Display\Display';
        $question_display = new $class($formvalidator, $complex_content_object_question, $question_nr, $question);
        return $question_display;
    }

    /**
     *
     * @author Antonio Ognio @source http://www.php.net/manual/en/function.shuffle.php (06-May-2008 04:42)
     */
    public function shuffle_with_keys($array)
    {
        /*
         * Auxiliary array to hold the new order
         */
        $aux = array();
        /*
         * We work with an array of the keys
         */
        $keys = array_keys($array);
        /*
         * We shuffle the keys
         */
        shuffle($keys);
        /*
         * We iterate thru' the new order of the keys
         */
        foreach ($keys as $key)
        {
            /*
             * We insert the key, value pair in its new order
             */
            $aux[(string) $key] = $array[$key];
            /*
             * We remove the element from the old array to save memory
             */
        }
        /*
         * The auxiliary array with the new order overwrites the old variable
         */
        return $aux;
    }
}
