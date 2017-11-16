<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use MediawikiParser;
use MediawikiParserContext;

/**
 *
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This viewer will show the selected wiki_page. You'll be redirected here from the wiki_viewer page by clicking on the
 * name of a wiki_page Author: Stefan Billiet Author: Nick De Feyter
 */
require_once Path::getInstance()->getPluginPath() . 'wiki/mediawiki_parser.class.php';
require_once Path::getInstance()->getPluginPath() . 'wiki/mediawiki_parser_context.class.php';
class WikiItemViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        BreadcrumbTrail::getInstance()->add(new Breadcrumb(null, $this->get_root_content_object()->get_title()));

        $complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($complex_wiki_page_id)
        {
            $version_object_id = Request::get(self::PARAM_WIKI_VERSION_ID);
            $complex_wiki_page = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class_name(),
                $complex_wiki_page_id);
            $wiki_page = $complex_wiki_page->get_ref_object();

            BreadcrumbTrail::getInstance()->add(new Breadcrumb(null, $wiki_page->get_title()));

            if ($version_object_id)
            {
                $version_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $version_object_id);
                if ($version_object && $version_object->get_object_number() == $wiki_page->get_object_number())
                {
                    $display_wiki_page = $version_object;
                }
                else
                {
                    $display_wiki_page = $wiki_page;
                }

                if ($wiki_page->get_id() == $version_object_id)
                {
                    Request::set_get(self::PARAM_WIKI_VERSION_ID, null);
                }
            }
            else
            {
                $display_wiki_page = $wiki_page;
            }

            $html = array();

            $html[] = $this->render_header($complex_wiki_page);
            $html[] = '<div class="wiki-pane-content-title">' . $display_wiki_page->get_title() . '</div>';
            $html[] = '<div class="wiki-pane-content-subtitle">' . Translation::get(
                'From',
                null,
                Utilities::COMMON_LIBRARIES) . ' ' . $this->get_root_content_object()->get_title() . '</div>';

            if ($version_object_id && $wiki_page->get_id() != $version_object_id)
            {
                $html[] = '<div class="wiki-pane-content-version">' . Translation::get('WikiOldVersion') . '</div>';
            }

            $parser = new MediawikiParser(
                new MediawikiParserContext(
                    $this->get_root_content_object(),
                    $display_wiki_page->get_title(),
                    $display_wiki_page->get_description(),
                    $this->get_parameters()));

            $display_wiki_page->set_description($parser->parse());
            $display = ContentObjectRenditionImplementation::factory(
                $display_wiki_page,
                ContentObjectRendition::FORMAT_HTML,
                ContentObjectRendition::VIEW_DESCRIPTION,
                $this);

            $html[] = '<div class="wiki-pane-content-body">';
            $html[] = $display->render();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->redirect(null, false, array(self::PARAM_ACTION => self::ACTION_VIEW_WIKI));
        }
    }
}
