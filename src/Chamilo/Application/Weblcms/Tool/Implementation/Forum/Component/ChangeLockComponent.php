<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_builder.forum.component
 */
class ChangeLockComponent extends Manager
{

    public function run()
    {
        $this->publication_id = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
        
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(), 
            $this->publication_id);
        
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }
        
        $object = $publication->get_content_object();
        if ($object->invert_locked())
        {
            $succes = true;
            $message = Translation :: get('LockChanged');
        }
        else
        {
            $message = Translation :: get('LockNotChanged');
        }
        
        $params = array();
        $params[self :: PARAM_ACTION] = self :: ACTION_BROWSE_FORUMS;
        
        $this->redirect($message, ! $succes, $params);
    }
}
