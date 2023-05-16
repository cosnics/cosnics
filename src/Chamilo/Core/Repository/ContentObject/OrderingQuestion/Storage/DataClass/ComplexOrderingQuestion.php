<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplaySupport;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * @package core\repository\content_object\ordering_question
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexOrderingQuestion extends ComplexContentObjectItem implements AnswerFeedbackDisplaySupport
{
    public const CONTEXT = OrderingQuestion::CONTEXT;

    public const PROPERTY_RANDOM = 'random';
    public const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';
    public const PROPERTY_WEIGHT = 'weight';

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_WEIGHT, self::PROPERTY_RANDOM, self::PROPERTY_SHOW_ANSWER_FEEDBACK];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_complex_ordering_question';
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
     * @return bool
     */
    public function get_random()
    {
        return $this->getAdditionalProperty(self::PROPERTY_RANDOM);
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
        return $this->getAdditionalProperty(self::PROPERTY_WEIGHT);
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
     * @param bool $value
     */
    public function set_random($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_RANDOM, $value);
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
