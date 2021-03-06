<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplaySupport;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 *
 * @package core\repository\content_object\assessment_rating_question
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexAssessmentRatingQuestion extends ComplexContentObjectItem implements AnswerFeedbackDisplaySupport
{
    const PROPERTY_WEIGHT = 'weight';
    const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';

    /**
     *
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_WEIGHT, self::PROPERTY_SHOW_ANSWER_FEEDBACK);
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
     *
     * @param int $value
     */
    public function set_weight($value)
    {
        $this->set_additional_property(self::PROPERTY_WEIGHT, $value);
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
     *
     * @param int $value
     */
    public function set_show_answer_feedback($value)
    {
        $this->set_additional_property(self::PROPERTY_SHOW_ANSWER_FEEDBACK, $value);
    }
}