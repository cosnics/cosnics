<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractAttempt;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application\weblcms\integration\core\tracking
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssessmentAttempt extends AbstractAttempt
{
    const PROPERTY_ASSESSMENT_ID = 'assessment_id';
    const PROPERTY_COURSE_ID = 'course_id';

    /**
     *
     * @return int
     */
    public function get_assessment_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ASSESSMENT_ID);
    }

    public function get_average_score($publication, $user_id = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($publication->get_id())
        );

        if ($user_id)
        {
            $conditions = [];
            $conditions[] = $condition;
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(self::class, self::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id)
            );
            $condition = new AndCondition($conditions);
        }

        $trackers = DataManager::retrieves(self::class, new DataClassRetrievesParameters($condition));
        $num = $trackers->count();

        $total_score = 0;

        foreach ($trackers as $tracker)
        {
            $total_score += $tracker->get_total_score();
        }

        $total_score = round($total_score / $num, 2);

        return $total_score;
    }

    /**
     *
     * @return int
     */
    public function get_course_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_ID);
    }

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_COURSE_ID;
        $extendedPropertyNames[] = self::PROPERTY_ASSESSMENT_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_assessment_attempt';
    }

    // TODO: These two methods should NOT be here as they are not related to ONE assessment attempt, but a collection of
    // attempts

    public function get_times_taken($publication, $user_id = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($publication->get_id())
        );

        if ($user_id)
        {
            $conditions = [];
            $conditions[] = $condition;
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(self::class, self::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id)
            );
            $condition = new AndCondition($conditions);
        }

        return DataManager::count(self::class, new DataClassCountParameters($condition));
    }

    /**
     *
     * @param int $assessment_id
     */
    public function set_assessment_id($assessment_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ASSESSMENT_ID, $assessment_id);
    }

    /**
     *
     * @param int $course_id
     */
    public function set_course_id($course_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_ID, $course_id);
    }
}
