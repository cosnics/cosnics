<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass;

/**
 *
 * @package repository.lib.question_types.fill_in_blanks_question
 */
use Chamilo\Libraries\Utilities\StringUtilities;

class FillInBlanksQuestionAnswer
{

    /**
     * If format is:
     * [answer(feedback)=score|answer]{hint}
     * then use this for regex
     * \[([^\[\]]*)\](?:\{([^\[\}]*)\})?
     * And to split individual questions into answers, possibly regex with own defined size,
     * with feedback and a (negative) score use
     * (\{regex(?:=([0-9]+))?\}.+?\{\/regex\}|(?!\{regex(?:=(?:[0-9]+))?\}).+?(?!\{\/regex\}))(?:\((.+?)\))?(?:=([+-]?[0-9]+))?(?:\||$)
     * The parts are parsed to give the following groups
     * 0 => entire string
     * 1 => value itself
     * 2 => size given by regex question (null otherwise)
     * 3 => feedback
     * 4 => score
     */
    const CLOZE_REGEX = '/\[[^\[\]]+\]/';
    const QUESTIONS_REGEX = '/\[([^[\]]+)\](?:\{([^[}]+)\})?/';
    const PARTS_REGEX = '/(\{regex(?:=([0-9]+))?\}.+?\{\/regex\}|(?!\{regex(?:=(?:[0-9]+))?\}).+?(?!\{\/regex\}))(?:\((.+?)\))?(?:=([+-]?[0-9]+))?(?:\||$)/';
    const REGEX_REGEX = '/(?:^\{regex(?:=([0-9]*))?\})(.*?)(?:\{\/regex\})$/';
    const REGEX_SIZE_REGEX = '';

    /**
     *
     * @param string $text formats question [answer 1(feedback 1)=score 1, answer 2=score 2, answer 3(feedback 3)]
     *        question 2 [answer 1, answer 2].
     * @param int $default_positive_score The score that should be added as default (when score is missing)
     * @return array of question's answers
     */
    public static function parse($text, $default_positive_score = FillInBlanksQuestion :: DEFAULT_POSITIVE_SCORE)
    {
        $result = array();

        $questions = array();
        preg_match_all(self::QUESTIONS_REGEX, $text, $questions);

        foreach ($questions[1] as $question_id => $question)
        {
            $answers = array();
            /*
             * The parts are parsed to give the following groups 0 => entire string 1 => value itself 2 => size given by
             * regex question (null otherwise) 3 => feedback 4 => score
             */
            preg_match_all(self::PARTS_REGEX, $question, $answers);

            foreach ($answers[1] as $answer_id => $answer)
            {
                $score = is_numeric($answers[4][$answer_id]) ? $answers[4][$answer_id] : $default_positive_score;
                $size = is_numeric($answers[2][$answer_id]) ? $answers[2][$answer_id] : '';
                $result[] = new FillInBlanksQuestionAnswer(
                    $answer,
                    $score,
                    $answers[3][$answer_id],
                    $size,
                    $question_id,
                    $questions[2][$question_id]);
            }
        }
        return $result;
    }

    /**
     * Get the best possible answer for a question,
     * based on it's weight / score
     *
     * @param array $answers
     * @return null FillInBlanksQuestionAnswer
     */
    public static function get_best_answer(array $answers)
    {
        $best_weight = 0;
        $best_answer = null;

        foreach ($answers as $key => $answer)
        {
            if ($answer->get_weight() > $best_weight)
            {
                $best_weight = $answer->get_weight();
                $best_answer = $answer;
            }
        }

        return $best_answer;
    }

    public static function get_number_of_questions($text)
    {
        $matches = array();
        return preg_match_all(self::QUESTIONS_REGEX, $text, $matches);
    }

    private $value;

    private $weight;

    private $comment;

    private $hint;

    private $size;

    private $position;

    public function __construct($value, $weight, $comment, $size, $position, $hint)
    {
        $this->value = $value;
        $this->weight = $weight;
        $this->comment = $comment;
        $this->hint = $hint;
        $this->size = empty($size) ? strlen($value) : $size;
        $this->position = $position;
    }

    public function get_comment()
    {
        return $this->comment;
    }

    public function has_comment()
    {
        return StringUtilities::getInstance()->hasValue($this->get_comment(), true);
    }

    public function get_hint()
    {
        return $this->hint;
    }

    public function has_hint()
    {
        return StringUtilities::getInstance()->hasValue($this->get_hint(), true);
    }

    public function get_value()
    {
        return $this->value;
    }

    public function get_weight()
    {
        return $this->weight;
    }

    public function get_size()
    {
        if ($this->check_regex())
        {
            return $this->regex_size();
        }
        return $this->size;
    }

    public function get_position()
    {
        return $this->position;
    }

    /**
     * Checks if the answer is a regex
     *
     * @return boolean
     */
    public function check_regex()
    {
        $pattern = self::REGEX_REGEX;
        return preg_match($pattern, $this->get_value());
    }

    /**
     * Returns the regex pattern described inside the {regex}-tags
     *
     * @return String regex pattern
     */
    public function get_regex_pattern()
    {
        $regex_parts = array();
        preg_match(self::REGEX_REGEX, $this->get_value(), $regex_parts);
        $pattern = $regex_parts[2];
        return $pattern;
    }

    /**
     * Returns the size given in the regex, null if no size was set.
     *
     * @return int or null
     */
    private function regex_size()
    {
        $regex_parts = array();
        preg_match(self::REGEX_REGEX, $this->get_value(), $regex_parts);
        return $regex_parts[1];
    }

    /**
     * Evaluates the answer.
     *
     * @param String $answer
     * @param boolean $case_sensitive
     * @return boolean
     */
    public function evaluate($answer, $case_sensitive)
    {
        // to make sure text fields that had no value are seen as empty, and not null.
        if ($answer == null)
        {
            $answer = '';
        }
        if ($this->check_regex())
        {
            return $this->evaluate_regex($answer);
        }
        return $this->evaluate_text($answer, $case_sensitive);
    }

    /**
     * Evaluates plain text.
     *
     * @param String $answer
     * @param boolean $case_sensitive
     * @return boolean
     */
    private function evaluate_text($answer, $case_sensitive)
    {
        $correct_answer = $this->get_value();
        if (! $case_sensitive)
        {
            $answer = mb_strtolower($answer, 'UTF-8');
            $correct_answer = mb_strtolower($correct_answer, 'UTF-8');
        }
        return $answer == $correct_answer;
    }

    /**
     * Evaluates regex.
     *
     * @param String $answer
     * @return boolean
     */
    private function evaluate_regex($answer)
    {
        $pattern = '/' . $this->get_regex_pattern() . '/';
        return preg_match($pattern, $answer);
    }
}
