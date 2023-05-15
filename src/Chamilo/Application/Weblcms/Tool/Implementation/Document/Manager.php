<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.document
 */

/**
 * This tool allows a user to publish documents in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements Categorizable, IntroductionTextSupportInterface
{
    public const ACTION_DOWNLOAD = 'Downloader';
    public const ACTION_SLIDESHOW = 'Slideshow';
    public const ACTION_SLIDESHOW_SETTINGS = 'SlideshowSettings';
    public const ACTION_VIEW_DOCUMENTS = 'Viewer';
    public const ACTION_ZIP_AND_DOWNLOAD = 'ZipAndDownload';

    public const CONTEXT = __NAMESPACE__;

    public function addContentObjectPublicationButtons(
        $publication, ButtonGroup $buttonGroup, DropdownButton $dropdownButton
    )
    {
        $class = $publication[ContentObject::PROPERTY_TYPE];
        $content_object = new $class($publication);
        $content_object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

        if ($content_object instanceof File || $content_object instanceof Webpage)
        {
            $buttonGroup->prependButton(
                new Button(
                    Translation::get('Download'), new FontAwesomeGlyph('download'), $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObject::PROPERTY_ID]
                    ]
                ), Button::DISPLAY_ICON, null, ['btn-link']
                )
            );
        }
    }

    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $class = $publication[ContentObject::PROPERTY_TYPE];
        $content_object = new $class($publication);
        $content_object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

        if ($content_object instanceof File || $content_object instanceof Webpage)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Download'), new FontAwesomeGlyph('download'), $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObject::PROPERTY_ID]
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
    }

    public static function get_allowed_types()
    {
        $allowed_types = [];

        $optional_types = [File::class, Webpage::class, Page::class];

        foreach ($optional_types as $optional_type)
        {
            if (ContentObject::is_available($optional_type))
            {
                $allowed_types[] = $optional_type;
            }
        }

        $hogentTypes = ['Hogent\Core\Repository\ContentObject\Video\Storage\DataClass\Video'];

        foreach ($hogentTypes as $hogentType)
        {
            if (class_exists($hogentType))
            {
                $allowed_types[] = $hogentType;
            }
        }

        return $allowed_types;
    }

    public function get_available_browser_types()
    {
        $browser_types = [];
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_GALLERY;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_SLIDESHOW;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }
}
