<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractAttempt;
use Chamilo\Libraries\Utilities\UUID;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DummyAttempt extends AbstractAttempt
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    /**
     *
     * @param string[] $extended_property_names
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update()
    {
        return PreviewStorage :: getInstance()->update_learning_path_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create()
    {
        $this->set_id(UUID :: v4());
        return PreviewStorage :: getInstance()->create_learning_path_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete()
    {
        return PreviewStorage :: getInstance()->delete_learning_path_attempt($this);
    }
}
