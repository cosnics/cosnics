<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
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
class LearningPathAttempt
    extends \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt
{
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     *
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_COURSE_ID;
        $extended_property_names[] = self::PROPERTY_PUBLICATION_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     *
     * @param int $course_id
     */
    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     *
     * @return int
     */
    public function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     *
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete()
    {
        $succes = parent::delete();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathChildAttempt::class_name(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($this->getId())
        );

        $trackers = DataManager::retrieves(
            LearningPathChildAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        while ($tracker = $trackers->next_result())
        {
            $succes &= $tracker->delete();
        }

        return $succes;
    }
}
