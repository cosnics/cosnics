<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package core\repository\content_object\assessment\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractQuestionAttempt extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ANSWER = 'answer';
    public const PROPERTY_FEEDBACK = 'feedback';
    public const PROPERTY_HINT = 'hint';
    public const PROPERTY_QUESTION_COMPLEX_ID = 'question_complex_id';
    public const PROPERTY_SCORE = 'score';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_QUESTION_COMPLEX_ID;
        $extendedPropertyNames[] = self::PROPERTY_ANSWER;
        $extendedPropertyNames[] = self::PROPERTY_FEEDBACK;
        $extendedPropertyNames[] = self::PROPERTY_SCORE;
        $extendedPropertyNames[] = self::PROPERTY_HINT;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public function get_answer()
    {
        return $this->getDefaultProperty(self::PROPERTY_ANSWER);
    }

    /**
     * @return string
     */
    public function get_feedback()
    {
        return $this->getDefaultProperty(self::PROPERTY_FEEDBACK);
    }

    /**
     * @return int
     */
    public function get_hint()
    {
        return $this->getDefaultProperty(self::PROPERTY_HINT);
    }

    /**
     * @return int
     */
    public function get_question_complex_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_QUESTION_COMPLEX_ID);
    }

    /**
     * @return int
     */
    public function get_score()
    {
        return $this->getDefaultProperty(self::PROPERTY_SCORE);
    }

    /**
     * @param string $answer
     */
    public function set_answer($answer)
    {
        $this->setDefaultProperty(self::PROPERTY_ANSWER, $answer);
    }

    /**
     * @param string $feedback
     */
    public function set_feedback($feedback)
    {
        $this->setDefaultProperty(self::PROPERTY_FEEDBACK, $feedback);
    }

    /**
     * @param int $hint
     */
    public function set_hint($hint)
    {
        $this->setDefaultProperty(self::PROPERTY_HINT, $hint);
    }

    /**
     * @param int $question_complex_id
     */
    public function set_question_complex_id($question_complex_id)
    {
        $this->setDefaultProperty(self::PROPERTY_QUESTION_COMPLEX_ID, $question_complex_id);
    }

    /**
     * @param int $score
     */
    public function set_score($score)
    {
        $this->setDefaultProperty(self::PROPERTY_SCORE, $score);
    }
}
