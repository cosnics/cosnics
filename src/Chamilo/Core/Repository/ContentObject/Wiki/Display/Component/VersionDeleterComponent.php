<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VersionDeleterComponent extends Manager
{

    public function run()
    {
        $complex_wiki_page_id = $this->getRequest()->query->get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($complex_wiki_page_id)
        {
            $object_id = $this->getRequest()->query->get(self::PARAM_WIKI_VERSION_ID);
            $complex_wiki_page = DataManager::retrieve_by_id(
                ComplexContentObjectItem::class, $complex_wiki_page_id
            );
            $wiki_page = $complex_wiki_page->get_ref_object();

            if ($object_id)
            {
                $object = DataManager::retrieve_by_id(
                    ContentObject::class, $object_id
                );

                $delete_allowed = DataManager::content_object_deletion_allowed(
                    $object, 'version'
                );
                if ($delete_allowed)
                {
                    if ($object->delete(true))
                    {
                        $this->redirectWithMessage(
                            Translation::get(
                                'ObjectDeleted', ['OBJECT' => Translation::get('WikiPageVersion')],
                                StringUtilities::LIBRARIES
                            ), false, [
                                self::PARAM_ACTION => self::ACTION_HISTORY,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                            ]
                        );
                    }
                    else
                    {
                        $this->redirectWithMessage(
                            Translation::get(
                                'ObjectNotDeleted', ['OBJECT' => Translation::get('WikiPageVersion')],
                                StringUtilities::LIBRARIES
                            ), true, [
                                self::PARAM_ACTION => self::ACTION_HISTORY,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                            ]
                        );
                    }
                }
                else
                {
                    $this->redirectWithMessage(
                        Translation::get(
                            'ObjectNotDeleted', ['OBJECT' => Translation::get('WikiPageVersion')],
                            StringUtilities::LIBRARIES
                        ), true, [
                            self::PARAM_ACTION => self::ACTION_HISTORY,
                            self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                        ]
                    );
                }
            }
            else
            {
                $this->redirectWithMessage(
                    Translation::get(
                        'ObjectNotDeleted', ['OBJECT' => Translation::get('WikiPageVersion')],
                        StringUtilities::LIBRARIES
                    ), true, [
                        self::PARAM_ACTION => self::ACTION_HISTORY,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page_id
                    ]
                );
            }
        }
        else
        {
            $this->redirectWithMessage(null, false, [self::PARAM_ACTION => self::ACTION_VIEW_WIKI]);
        }
    }
}
