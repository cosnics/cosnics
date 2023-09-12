<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use MediawikiParser;
use MediawikiParserContext;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if ($this->get_root_content_object() != null)
        {
            $complex_wiki_homepage = $this->get_wiki_homepage($this->get_root_content_object_id());

            if (!is_null($complex_wiki_homepage))
            {
                $this->getRequest()->request->set(
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_wiki_homepage->get_id()
                );

                $wiki_homepage = $complex_wiki_homepage->get_ref_object();

                $parser = new MediawikiParser(
                    new MediawikiParserContext(
                        $this->get_root_content_object(), $wiki_homepage->get_title(),
                        $wiki_homepage->get_description(), $this->get_parameters()
                    )
                );

                $html = [];

                $html[] = $this->render_header($complex_wiki_homepage);

                $html[] = '<div class="panel panel-default">';
                $html[] = '<div class="panel-heading">';
                $html[] = '<h3 class="panel-title">';

                $html[] = $wiki_homepage->get_title();

                $html[] = '</h3>';

                $html[] = '</div>';

                $parser = new MediawikiParser(
                    new MediawikiParserContext(
                        $this->get_root_content_object(), $wiki_homepage->get_title(),
                        $wiki_homepage->get_description(), $this->get_parameters()
                    )
                );

                $wiki_homepage->set_description($parser->parse());
                $display = ContentObjectRenditionImplementation::factory(
                    $wiki_homepage, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
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
                $this->redirectWithMessage(
                    Translation::get('PleaseConfigureWikiHomepage'), false,
                    [self::PARAM_ACTION => self::ACTION_BROWSE_WIKI]
                );
            }
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
    }
}
