<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class LinksDeleterComponent extends Manager
{

    public function run()
    {
        $pub_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, 
            $pub_id);
        
        $publication->set_show_on_homepage(0);
        $succes = $publication->update();
        
        $message = $succes ? 'PublicationRemovedFromHomepage' : 'PublicationNotRemovedFromHomepage';
        
        $this->redirect(Translation::get($message), ! $succes, array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}
