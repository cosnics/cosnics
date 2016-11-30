<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 *
 * @package core\repository\content_object\assessment\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnswerFeedbackDisplay
{

    /**
     *
     * @var \core\repository\content_object\assessment\display\Configuration
     */
    private $configuration;

    /**
     *
     * @var \core\repository\storage\data_class\ComplexContentObjectItem
     */
    private $complex_question;

    /**
     *
     * @var boolean
     */
    private $is_given_answer;

    /**
     *
     * @var boolean
     */
    private $is_correct;

    /**
     *
     * @param \core\repository\content_object\assessment\display\Configuration $configuration
     * @param \core\repository\storage\data_class\ComplexContentObjectItem $complex_question
     * @param boolean $is_given_answer
     * @param boolean $is_correct
     */
    public function __construct(Configuration $configuration, ComplexContentObjectItem $complex_question, 
        $is_given_answer, $is_correct)
    {
        if (! $complex_question instanceof AnswerFeedbackDisplaySupport)
        {
            throw new \Exception(
                'ComplexContentObjectItem needs to implement the AnswerFeedbackDisplaySupport interface');
        }
        
        $this->configuration = $configuration;
        $this->complex_question = $complex_question;
        $this->is_given_answer = $is_given_answer;
        $this->is_correct = $is_correct;
    }

    /**
     *
     * @return \core\repository\content_object\assessment\display\Configuration
     */
    public function get_configuration()
    {
        return $this->configuration;
    }

    /**
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem
     */
    public function get_complex_question()
    {
        return $this->complex_question;
    }

    /**
     *
     * @return boolean
     */
    public function get_is_given_answer()
    {
        return $this->is_given_answer;
    }

    /**
     *
     * @return boolean
     */
    public function get_is_correct()
    {
        return $this->is_correct;
    }

    /**
     *
     * @return boolean
     */
    public function is_correct()
    {
        return $this->get_is_correct();
    }

    /**
     *
     * @return boolean
     */
    public function is_wrong()
    {
        return ! $this->get_is_correct();
    }

    /**
     *
     * @return boolean
     */
    public function is_given_answer()
    {
        return $this->get_is_given_answer();
    }

    /**
     *
     * @return boolean
     */
    public function run()
    {
        if ($this->get_configuration()->show_correction() || $this->get_configuration()->show_solution())
        {
            if (! $this->get_configuration()->show_solution())
            {
                if ($this->get_configuration()->show_answer_feedback() && $this->is_given_answer())
                {
                    return true;
                }
            }
            else
            {
                if ($this->get_configuration()->show_answer_feedback())
                {
                    if ($this->get_configuration()->get_show_answer_feedback() ==
                         Configuration::ANSWER_FEEDBACK_TYPE_QUESTION)
                    {
                        $show_answer_feedback = $this->get_complex_question()->get_show_answer_feedback();
                    }
                    else
                    {
                        $show_answer_feedback = $this->get_configuration()->get_show_answer_feedback();
                    }
                    
                    switch ($show_answer_feedback)
                    {
                        case Configuration::ANSWER_FEEDBACK_TYPE_ALL :
                            return true;
                            break;
                        case Configuration::ANSWER_FEEDBACK_TYPE_CORRECT :
                            return $this->is_correct();
                            break;
                        case Configuration::ANSWER_FEEDBACK_TYPE_WRONG :
                            return $this->is_wrong();
                            break;
                        case Configuration::ANSWER_FEEDBACK_TYPE_GIVEN :
                            return $this->is_given_answer();
                            break;
                        case Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_CORRECT :
                            return $this->is_given_answer() && $this->is_correct();
                            break;
                        case Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_WRONG :
                            return $this->is_given_answer() && $this->is_wrong();
                            break;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     *
     * @param Configuration $configuration
     * @param ComplexContentObjectItem $complex_question
     * @param boolean $is_given_answer
     * @param boolean $is_correct
     */
    public static function allowed(Configuration $configuration, ComplexContentObjectItem $complex_question, 
        $is_given_answer, $is_correct)
    {
        $answer_feedback_display = new self($configuration, $complex_question, $is_given_answer, $is_correct);
        return $answer_feedback_display->run();
    }
}
