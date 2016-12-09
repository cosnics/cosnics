<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component\GeolocationBrowser;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationBrowser;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: geolocation_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.geolocation.component.geolocation_browser
 */
class GeolocationBrowser extends ContentObjectPublicationBrowser
{

    public function __construct($parent, $types)
    {
        parent::__construct($parent, 'geolocation');
        
        $this->set_publication_id(Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID));
        $renderer = new GeolocationDetailsRenderer($this);
        
        $this->set_publication_list_renderer($renderer);
    }

    public function get_publications($from, $count, $column, $direction)
    {
    }

    public function get_publication_count()
    {
    }
}
