<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass\Matterhorn;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: document_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.document
 */

/**
 * This tool allows a user to publish documents in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable
{
    const ACTION_VIEW_DOCUMENTS = 'viewer';
    const ACTION_DOWNLOAD = 'downloader';
    const ACTION_ZIP_AND_DOWNLOAD = 'zip_and_download';
    const ACTION_SLIDESHOW = 'slideshow';
    const ACTION_SLIDESHOW_SETTINGS = 'slideshow_settings';

    public static function get_allowed_types()
    {
        $allowed_types = array();

        $optional_types = array(
            File :: class_name(),
            Webpage :: class_name(),
            Page :: class_name(),
            Matterhorn :: class_name());

        foreach ($optional_types as $optional_type)
        {
            if (ContentObject :: is_available($optional_type))
            {
                $allowed_types[] = $optional_type;
            }
        }

        return $allowed_types;
    }

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_GALLERY;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_SLIDESHOW;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        return $browser_types;
    }

    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $class = $publication[ContentObject :: PROPERTY_TYPE];
        $content_object = new $class($publication);
        $content_object->set_id($publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

        $allow_download_page = \Chamilo\Libraries\Platform\Configuration\PlatformSetting :: get(
            'allow_download_page',
            \Chamilo\Libraries\Architecture\ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                \Chamilo\Application\Weblcms\Manager :: class_name()));

        if (! $content_object instanceof Page || $allow_download_page)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Download'),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_download.png',
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_DOWNLOAD,
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObject :: PROPERTY_ID])),
                    ToolbarItem :: DISPLAY_ICON));
        }
    }
}
