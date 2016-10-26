<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: important.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_builder.forum.component
 */
class ImportantComponent extends Manager
{

    public function run()
    {
        $topic = $this->get_selected_complex_content_object_item();
        
        if ($topic->get_forum_type() == 2)
        {
            $topic->set_forum_type(null);
            $message = Translation :: get('TopicUnImportant');
        }
        else
        {
            $topic->set_forum_type(2);
            $message = Translation :: get('TopicImportant');
        }
        $success = $topic->update();
        
        if (! $success)
        {
            $message = Translation :: get(
                'ObjectNotUpdated', 
                array('OBJECT' => Translation :: get('ForumTopic')), 
                Utilities :: COMMON_LIBRARIES);
        }
        
        $params = array();
        $params[self :: PARAM_ACTION] = self :: ACTION_VIEW_FORUM;
        $params[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        $this->redirect($message, ($success ? false : true), $params);
    }
}
