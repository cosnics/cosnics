<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VersionReverterComponent extends Manager
{

    public function run()
    {
        $complex_wiki_page_id = $this->getRequest()->query->get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($complex_wiki_page_id)
        {
            $version_object_id = $this->getRequest()->query->get(self::PARAM_WIKI_VERSION_ID);
            $complex_wiki_page = DataManager::retrieve_by_id(
                ComplexWikiPage::class, $complex_wiki_page_id
            );
            $wiki_page = $complex_wiki_page->get_ref_object();

            if ($version_object_id)
            {
                $version_object = DataManager::retrieve_by_id(
                    ContentObject::class, $version_object_id
                );
                if ($version_object && $version_object->get_object_number() == $wiki_page->get_object_number())
                {
                    if ($version_object->version())
                    {
                        $complex_wiki_page->set_ref($version_object->get_latest_version_id());
                        if ($complex_wiki_page->update())
                        {
                            $this->redirectWithMessage(
                                Translation::get('WikiPageReverted'), false, array(
                                    self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                                )
                            );
                        }
                        else
                        {
                            $this->redirectWithMessage(
                                Translation::get('WikiPageRevertedPublicationNotUpdated'), true, array(
                                    self::PARAM_ACTION => self::ACTION_HISTORY,
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                                )
                            );
                        }
                    }
                    else
                    {
                        $this->redirectWithMessage(
                            Translation::get('WikiPageNotReverted'), true, array(
                                self::PARAM_ACTION => self::ACTION_HISTORY,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                            )
                        );
                    }
                }
                else
                {
                    $this->redirectWithMessage(
                        Translation::get('WikiPageNotReverted'), true, array(
                            self::PARAM_ACTION => self::ACTION_HISTORY,
                            self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                        )
                    );
                }
            }
            else
            {
                $this->redirectWithMessage(
                    Translation::get('WikiPageNotReverted'), true, array(
                        self::PARAM_ACTION => self::ACTION_HISTORY,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                    )
                );
            }
        }
        else
        {
            $this->redirectWithMessage(null, false, array(self::PARAM_ACTION => self::ACTION_VIEW_WIKI));
        }
    }
}
