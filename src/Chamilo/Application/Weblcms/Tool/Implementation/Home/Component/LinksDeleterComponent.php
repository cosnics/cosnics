<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;
use Chamilo\Libraries\Translation\Translation;

class LinksDeleterComponent extends Manager
{

    public function run()
    {
        $pub_id = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $pub_id
        );

        $publication->set_show_on_homepage(0);
        $succes = $publication->update();

        $message = $succes ? 'PublicationRemovedFromHomepage' : 'PublicationNotRemovedFromHomepage';

        $this->redirectWithMessage(Translation::get($message), !$succes, [self::PARAM_ACTION => self::ACTION_BROWSE]);
    }
}
