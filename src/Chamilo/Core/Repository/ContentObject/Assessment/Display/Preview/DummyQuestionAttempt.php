<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractQuestionAttempt;
use Chamilo\Libraries\Utilities\UUID;

/**
 *
 * @package core\repository\content_object\assessment\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DummyQuestionAttempt extends AbstractQuestionAttempt
{
    const PROPERTY_ATTEMPT_ID = 'attempt_id';

    /**
     *
     * @param string[] $extended_property_names
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ATTEMPT_ID;
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_attempt_id()
    {
        return $this->get_default_property(self :: PROPERTY_ATTEMPT_ID);
    }

    /**
     *
     * @param int $attempt_id
     */
    public function set_attempt_id($attempt_id)
    {
        $this->set_default_property(self :: PROPERTY_ATTEMPT_ID, $attempt_id);
    }

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update()
    {
        return PreviewStorage :: get_instance()->update_assessment_question_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create()
    {
        $this->set_id(UUID :: v4());
        return PreviewStorage :: get_instance()->create_assessment_question_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete()
    {
        return PreviewStorage :: get_instance()->delete_assessment_question_attempt($this);
    }
}
