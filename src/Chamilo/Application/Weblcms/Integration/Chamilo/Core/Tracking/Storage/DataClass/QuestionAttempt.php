<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractQuestionAttempt;

/**
 *
 * @package application\weblcms\integration\core\tracking
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class QuestionAttempt extends AbstractQuestionAttempt

{
    const PROPERTY_ASSESSMENT_ATTEMPT_ID = 'assessment_attempt_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ASSESSMENT_ATTEMPT_ID;
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function get_assessment_attempt_id()
    {
        return $this->get_default_property(self::PROPERTY_ASSESSMENT_ATTEMPT_ID);
    }

    /**
     *
     * @param int $assessment_attempt_id
     */
    public function set_assessment_attempt_id($assessment_attempt_id)
    {
        $this->set_default_property(self::PROPERTY_ASSESSMENT_ATTEMPT_ID, $assessment_attempt_id);
    }
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'tracking_weblcms_question_attempt';
    }

}
