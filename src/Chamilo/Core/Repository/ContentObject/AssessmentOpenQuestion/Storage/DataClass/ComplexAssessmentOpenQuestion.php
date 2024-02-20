<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplaySupport;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;

/**
 * @package core\repository\content_object\assessment_open_question
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexAssessmentOpenQuestion extends ComplexContentObjectItem
    implements AnswerFeedbackDisplaySupport, DataClassExtensionInterface
{
    public const CONTEXT = AssessmentOpenQuestion::CONTEXT;

    public const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';
    public const PROPERTY_WEIGHT = 'weight';

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array
    {
        return parent::getAdditionalPropertyNames([self::PROPERTY_WEIGHT, self::PROPERTY_SHOW_ANSWER_FEEDBACK]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_complex_assessment_open_question';
    }

    /**
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
     * @return int
     */
    public function get_show_answer_feedback()
    {
        return $this->getAdditionalProperty(self::PROPERTY_SHOW_ANSWER_FEEDBACK);
    }

    /**
     * @return int
     */
    public function get_weight()
    {
        return $this->getAdditionalProperty(self::PROPERTY_WEIGHT, self::PROPERTY_SHOW_ANSWER_FEEDBACK);
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

    // TODO: should be moved to an additional parent layer "complex_question" which offers this implementation to EACH
    // complex question object.

    /**
     * @param int $value
     */
    public function set_show_answer_feedback($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_SHOW_ANSWER_FEEDBACK, $value);
    }

    /**
     * @param int $value
     */
    public function set_weight($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_WEIGHT, $value);
    }
}
