<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This viewer will show the selected wiki_page. You'll be redirected here from the wiki_viewer page by clicking on the
 * name of a wiki_page Author: Stefan Billiet Author: Nick De Feyter
 */
class VersionReverterComponent extends Manager
{

    public function run()
    {
        $complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($complex_wiki_page_id)
        {
            $version_object_id = Request::get(self::PARAM_WIKI_VERSION_ID);
            $complex_wiki_page = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexWikiPage::class_name(),
                $complex_wiki_page_id);
            $wiki_page = $complex_wiki_page->get_ref_object();

            if ($version_object_id)
            {
                $version_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $version_object_id);
                if ($version_object && $version_object->get_object_number() == $wiki_page->get_object_number())
                {
                    if ($version_object->version())
                    {
                        $complex_wiki_page->set_ref($version_object->get_latest_version_id());
                        if ($complex_wiki_page->update())
                        {
                            $this->redirect(
                                Translation::get('WikiPageReverted'),
                                false,
                                array(
                                    self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                        }
                        else
                        {
                            $this->redirect(
                                Translation::get('WikiPageRevertedPublicationNotUpdated'),
                                true,
                                array(
                                    self::PARAM_ACTION => self::ACTION_HISTORY,
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                        }
                    }
                    else
                    {
                        $this->redirect(
                            Translation::get('WikiPageNotReverted'),
                            true,
                            array(
                                self::PARAM_ACTION => self::ACTION_HISTORY,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                    }
                }
                else
                {
                    $this->redirect(
                        Translation::get('WikiPageNotReverted'),
                        true,
                        array(
                            self::PARAM_ACTION => self::ACTION_HISTORY,
                            self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
                }
            }
            else
            {
                $this->redirect(
                    Translation::get('WikiPageNotReverted'),
                    true,
                    array(
                        self::PARAM_ACTION => self::ACTION_HISTORY,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id));
            }
        }
        else
        {
            $this->redirect(null, false, array(self::PARAM_ACTION => self::ACTION_VIEW_WIKI));
        }
    }
}
