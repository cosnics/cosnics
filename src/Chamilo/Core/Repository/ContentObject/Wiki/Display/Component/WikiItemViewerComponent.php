<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use MediawikiParser;
use MediawikiParserContext;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 * @author  Stefan Billiet
 * @author  Nick De Feyter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WikiItemViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $this->getBreadcrumbTrail()->add(new Breadcrumb(null, $this->get_root_content_object()->get_title()));

        $complex_wiki_page_id = $this->getRequest()->query->get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($complex_wiki_page_id)
        {
            $version_object_id = $this->getRequest()->query->get(self::PARAM_WIKI_VERSION_ID);
            $complex_wiki_page = DataManager::retrieve_by_id(
                ComplexContentObjectItem::class, $complex_wiki_page_id
            );
            $wiki_page = $complex_wiki_page->get_ref_object();

            $this->getBreadcrumbTrail()->add(new Breadcrumb(null, $wiki_page->get_title()));

            if ($version_object_id)
            {
                $version_object = DataManager::retrieve_by_id(
                    ContentObject::class, $version_object_id
                );
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
                    $this->getRequest()->request->set(self::PARAM_WIKI_VERSION_ID, null);
                }
            }
            else
            {
                $display_wiki_page = $wiki_page;
            }

            $html = [];

            $html[] = $this->render_header($complex_wiki_page);

            $isOldVersion = $version_object_id && $wiki_page->get_id() != $version_object_id;

            $panelType = $isOldVersion ? 'panel-warning' : 'panel-default';

            $html[] = '<div class="panel ' . $panelType . '">';
            $html[] = '<div class="panel-heading">';

            $html[] = '<h3 class="panel-title">';

            if ($isOldVersion)
            {
                $html[] = '<strong>[' . Translation::get('WikiOldVersion') . ']</strong> ';
            }

            $html[] = $display_wiki_page->get_title();

            $html[] = '</h3>';

            $html[] = '</div>';

            $parser = new MediawikiParser(
                new MediawikiParserContext(
                    $this->get_root_content_object(), $display_wiki_page->get_title(),
                    $display_wiki_page->get_description(), $this->get_parameters()
                )
            );

            $display_wiki_page->set_description($parser->parse());
            $display = ContentObjectRenditionImplementation::factory(
                $display_wiki_page, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
            );

            $html[] = '<div class="panel-body">';
            $html[] = $display->render();
            $html[] = '</div>';

            $html[] = '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->redirectWithMessage(null, false, [self::PARAM_ACTION => self::ACTION_VIEW_WIKI]);
        }
    }
}
