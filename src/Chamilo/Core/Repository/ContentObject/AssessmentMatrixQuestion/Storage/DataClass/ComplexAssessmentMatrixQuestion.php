<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplaySupport;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 *
 * @package core\repository\content_object\assessment_matrix_question
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexAssessmentMatrixQuestion extends ComplexContentObjectItem implements AnswerFeedbackDisplaySupport
{
    const PROPERTY_RANDOM = 'random';
    const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';
    const PROPERTY_WEIGHT = 'weight';

    /**
     *
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_WEIGHT, self::PROPERTY_RANDOM, self::PROPERTY_SHOW_ANSWER_FEEDBACK);
    }

    /**
     *
     * @return int
     */
    public function get_default_question_weight()
    {
        $reference = parent::get_ref_object();
        if ($reference)
        {
            return $reference->get_default_weight();
        }

        return 1;
    }

    /**
     *
     * @return boolean
     */
    public function get_random()
    {
        return $this->get_additional_property(self::PROPERTY_RANDOM);
    }

    /**
     *
     * @return int
     */
    public function get_show_answer_feedback()
    {
        return $this->get_additional_property(self::PROPERTY_SHOW_ANSWER_FEEDBACK);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_complex_assessment_matrix_question';
    }

    /**
     *
     * @return int
     */
    public function get_weight()
    {
        return $this->get_additional_property(self::PROPERTY_WEIGHT);
    }

    /**
     * If we set the parent, we update the default weight to the parents default.
     */
    public function set_parent($parent)
    {
        // TODO: should be moved to an additional parent layer "complex_question" which offers this implementation to
        // EACH
        // complex question object.
        parent::set_parent($parent);
        $this->set_weight($this->get_default_question_weight());
    }

    /**
     *
     * @param boolean $value
     */
    public function set_random($value)
    {
        $this->set_additional_property(self::PROPERTY_RANDOM, $value);
    }

    // TODO: should be moved to an additional parent layer "complex_question" which offers this implementation to EACH
    // complex question object.

    /**
     *
     * @param int $value
     */
    public function set_show_answer_feedback($value)
    {
        $this->set_additional_property(self::PROPERTY_SHOW_ANSWER_FEEDBACK, $value);
    }

    /**
     *
     * @param int $value
     */
    public function set_weight($value)
    {
        $this->set_additional_property(self::PROPERTY_WEIGHT, $value);
    }
}
