<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractAttempt extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_PROGRESS = 'progress';

    /**
     *
     * @param string[] $extended_property_names
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_USER_ID;
        $extended_property_names[] = self::PROPERTY_PROGRESS;
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @return int
     */
    public function get_progress()
    {
        return $this->get_default_property(self::PROPERTY_PROGRESS);
    }

    /**
     *
     * @param int $progress
     */
    public function set_progress($progress)
    {
        $this->set_default_property(self::PROPERTY_PROGRESS, $progress);
    }
}
