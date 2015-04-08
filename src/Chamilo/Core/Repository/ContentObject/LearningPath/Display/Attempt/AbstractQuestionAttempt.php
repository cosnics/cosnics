<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt;

/**
 *
 * @package core\repository\content_object\learning_path\display$LearningPathQuestionAttempt
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractQuestionAttempt extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractQuestionAttempt
{
    const PROPERTY_ITEM_ATTEMPT_ID = 'item_attempt_id';

    /**
     *
     * @param string[] $extended_property_names
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ITEM_ATTEMPT_ID;
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_item_attempt_id()
    {
        return $this->get_default_property(self :: PROPERTY_ITEM_ATTEMPT_ID);
    }

    /**
     *
     * @param int $item_attempt_id
     */
    public function set_item_attempt_id($item_attempt_id)
    {
        $this->set_default_property(self :: PROPERTY_ITEM_ATTEMPT_ID, $item_attempt_id);
    }
}
