<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the component that allows the user to make a wiki_page the homepage. Author: Stefan Billiet Author: Nick De
 * Feyter
 */
class WikiHomepageSetterComponent extends Manager
{

    public function run()
    {
        $page = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(),
            Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        /*
         * If the wiki_page isn't empy the homepage will be set
         */
        if (! empty($page))
        {
            $page->set_is_homepage(true);
            $page->update();
            $this->redirect(
                Translation::get('HomepageSelected'),
                false,
                array(self::PARAM_ACTION => self::ACTION_VIEW_WIKI, 'pid' => Request::get('pid')));
        }
        else
        {
            $this->redirect(
                Translation::get('HomepageNotSelected'),
                true,
                array(self::PARAM_ACTION => self::ACTION_BROWSE_WIKI));
        }
    }
}
