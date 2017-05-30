<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass\Matterhorn;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
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
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable, 
    IntroductionTextSupportInterface
{
    const ACTION_VIEW_DOCUMENTS = 'Viewer';
    const ACTION_DOWNLOAD = 'Downloader';
    const ACTION_ZIP_AND_DOWNLOAD = 'ZipAndDownload';
    const ACTION_SLIDESHOW = 'Slideshow';
    const ACTION_SLIDESHOW_SETTINGS = 'SlideshowSettings';

    public static function get_allowed_types()
    {
        $allowed_types = array();
        
        $optional_types = array(
            File::class_name(), 
            Webpage::class_name(), 
            Page::class_name(), 
            Matterhorn::class_name());
        
        foreach ($optional_types as $optional_type)
        {
            if (ContentObject::is_available($optional_type))
            {
                $allowed_types[] = $optional_type;
            }
        }

        $hogentTypes = array(
            'Hogent\Core\Repository\ContentObject\Video\Storage\DataClass\Video'
        );

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
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_GALLERY;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_SLIDESHOW;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;
        return $browser_types;
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
                    Translation::get('Download'), 
                    Theme::getInstance()->getCommonImagePath('Action/Download'), 
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObject::PROPERTY_ID])), 
                    ToolbarItem::DISPLAY_ICON));
        }
    }

    public function addContentObjectPublicationButtons($publication, ButtonGroup $buttonGroup, 
        DropdownButton $dropdownButton)
    {
        $class = $publication[ContentObject::PROPERTY_TYPE];
        $content_object = new $class($publication);
        $content_object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

        if ($content_object instanceof File || $content_object instanceof Webpage)
        {
            $buttonGroup->prependButton(
                new Button(
                    Translation::get('Download'), 
                    new BootstrapGlyph('download'), 
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObject::PROPERTY_ID])), 
                    Button::DISPLAY_ICON, 
                    false, 
                    'btn-link'));
        }
    }
}
