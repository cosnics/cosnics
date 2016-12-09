<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Libraries\Utilities\UUID;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DummyItemAttempt extends AbstractItemAttempt
{

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update()
    {
        return PreviewStorage::getInstance()->update_learning_path_item_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create()
    {
        $this->set_id(UUID::v4());
        return PreviewStorage::getInstance()->create_learning_path_item_attempt($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete()
    {
        return PreviewStorage::getInstance()->delete_learning_path_item_attempt($this);
    }
}
