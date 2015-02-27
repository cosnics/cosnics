<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Component;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use MediawikiParser;
use MediawikiParserContext;

/**
 * $Id: wiki_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the compenent that allows the user to view all pages of a wiki. If no homepage is set all available pages
 * will be shown, otherwise the homepage will be shown. Author: Stefan Billiet Author: Nick De Feyter
 */
class ViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if ($this->get_root_content_object() != null)
        {
            $complex_wiki_homepage = $this->get_wiki_homepage($this->get_root_content_object_id());

            if (! is_null($complex_wiki_homepage))
            {
                Request :: set_get(
                    self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                    $complex_wiki_homepage->get_id());

                $wiki_homepage = $complex_wiki_homepage->get_ref_object();

                $parser = new MediawikiParser(
                    new MediawikiParserContext(
                        $this->get_root_content_object(),
                        $wiki_homepage->get_title(),
                        $wiki_homepage->get_description(),
                        $this->get_parameters()));

                $html = array();

                $html[] = $this->render_header($complex_wiki_homepage);
                $html[] = '<div class="wiki-pane-content-title">' . $wiki_homepage->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-subtitle">' .
                     Translation :: get('From', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                     $this->get_root_content_object()->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-body">';
                $html[] = $parser->parse();
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
            else
            {
                $this->redirect(
                    Translation :: get('PleaseConfigureWikiHomepage'),
                    false,
                    array(self :: PARAM_ACTION => self :: ACTION_BROWSE_WIKI));
            }
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = $this->get_breadcrumbtrail();
    }
}
