<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 *
 * @author Stefan Billiet
 * @author Nick De Feyter
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WikiHomepageSetterComponent extends Manager
{

    public function run()
    {
        $page = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $this->getRequest()->query->get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)
        );

        if (!empty($page))
        {
            $page->set_is_homepage(true);
            $page->update();
            $this->redirectWithMessage(
                Translation::get('HomepageSelected'), false,
                array(self::PARAM_ACTION => self::ACTION_VIEW_WIKI, 'pid' => $this->getRequest()->query->get('pid'))
            );
        }
        else
        {
            $this->redirectWithMessage(
                Translation::get('HomepageNotSelected'), true, array(self::PARAM_ACTION => self::ACTION_BROWSE_WIKI)
            );
        }
    }
}
