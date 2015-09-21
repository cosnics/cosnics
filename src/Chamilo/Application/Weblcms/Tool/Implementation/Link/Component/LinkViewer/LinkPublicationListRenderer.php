<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Link\Component\LinkViewer;

use Chamilo\Application\Weblcms\Renderer\PublicationList\Type\ListContentObjectPublicationListRenderer;

/**
 * $Id: link_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z
 * kariboe $
 * 
 * @package application.lib.weblcms.tool.link.component.link_viewer
 */
class LinkPublicationListRenderer extends ListContentObjectPublicationListRenderer
{

    public function __construct($browser)
    {
        parent :: __construct($browser);
    }

    public function render_title($publication)
    {
        $url = $publication->get_content_object()->get_url();
        return '<a target="about:blank" href="' . htmlentities($url) . '">' . parent :: render_title($publication) .
             '</a>';
    }
}
