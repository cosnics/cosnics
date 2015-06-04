<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: delete.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.component
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        if (Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID))
        {
            $publication_ids = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
        }
        else
        {
            $publication_ids = $_POST[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID];
        }
        
        if (! is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }
        
        $failures = 0;
        
        foreach ($publication_ids as $pid)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                ContentObjectPublication :: class_name(), 
                $pid);
            
            $content_object = $publication->get_content_object();
            
            if ($content_object->get_type() == Introduction :: class_name())
            {
                $publication->ignore_display_order();
            }
            
            if ($this->is_allowed(WeblcmsRights :: DELETE_RIGHT, $publication))
            {
                $publication->delete();
            }
            else
            {
                $failures ++;
            }
        }
        if ($failures == 0)
        {
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationDeleted'));
            }
        }
        else
        {
            $message = htmlentities(Translation :: get('ContentObjectPublicationsNotDeleted'));
        }
        
        $this->redirect(
            $message, 
            $failures > 0, 
            array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
    }
}
