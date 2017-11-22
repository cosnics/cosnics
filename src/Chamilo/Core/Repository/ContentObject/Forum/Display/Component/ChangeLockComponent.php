<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.complex_builder.forum.component
 */
class ChangeLockComponent extends Manager
{

    public function run()
    {
        $wrapper = $this->get_selected_complex_content_object_item();
        $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $wrapper->get_ref());

        if ($object->invert_locked())
        {
            $succes = true;
            $message = Translation::get('LockChanged');
        }
        else
        {
            $message = Translation::get('LockNotChanged');
        }

        $params = array();
        if ($object->get_type() == Forum::class_name())
        {
            $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        }
        else
        {
            $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        }
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, ! $succes, $params);
    }
}
