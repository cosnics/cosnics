<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package core\repository\content_object\assessment\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractQuestionAttempt extends DataClass
{
    const PROPERTY_QUESTION_COMPLEX_ID = 'question_complex_id';
    const PROPERTY_ANSWER = 'answer';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_HINT = 'hint';

    /**
     *
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_QUESTION_COMPLEX_ID;
        $extendedPropertyNames[] = self::PROPERTY_ANSWER;
        $extendedPropertyNames[] = self::PROPERTY_FEEDBACK;
        $extendedPropertyNames[] = self::PROPERTY_SCORE;
        $extendedPropertyNames[] = self::PROPERTY_HINT;
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function get_question_complex_id()
    {
        return $this->get_default_property(self::PROPERTY_QUESTION_COMPLEX_ID);
    }

    /**
     *
     * @param int $question_complex_id
     */
    public function set_question_complex_id($question_complex_id)
    {
        $this->set_default_property(self::PROPERTY_QUESTION_COMPLEX_ID, $question_complex_id);
    }

    /**
     *
     * @return string
     */
    public function get_answer()
    {
        return $this->get_default_property(self::PROPERTY_ANSWER);
    }

    /**
     *
     * @param string $answer
     */
    public function set_answer($answer)
    {
        $this->set_default_property(self::PROPERTY_ANSWER, $answer);
    }

    /**
     *
     * @return int
     */
    public function get_score()
    {
        return $this->get_default_property(self::PROPERTY_SCORE);
    }

    /**
     *
     * @param int $score
     */
    public function set_score($score)
    {
        $this->set_default_property(self::PROPERTY_SCORE, $score);
    }

    /**
     *
     * @return string
     */
    public function get_feedback()
    {
        return $this->get_default_property(self::PROPERTY_FEEDBACK);
    }

    /**
     *
     * @param string $feedback
     */
    public function set_feedback($feedback)
    {
        $this->set_default_property(self::PROPERTY_FEEDBACK, $feedback);
    }

    /**
     *
     * @return int
     */
    public function get_hint()
    {
        return $this->get_default_property(self::PROPERTY_HINT);
    }

    /**
     *
     * @param int $hint
     */
    public function set_hint($hint)
    {
        $this->set_default_property(self::PROPERTY_HINT, $hint);
    }
}
