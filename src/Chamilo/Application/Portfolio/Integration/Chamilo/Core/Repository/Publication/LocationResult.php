<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\Location\ContextLocationResult;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package application\personal_calendar\integration\core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LocationResult extends ContextLocationResult
{

    /**
     *
     * @see \core\repository\publication\LocationResult::get_link()
     */
    public function get_link(\Chamilo\Core\Repository\Publication\LocationSupport $location, $result)
    {
        $portfolioUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Portfolio\Manager::package(), 
                \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager::PARAM_STEP => $result->get_id()));
        
        return $portfolioUrl->getUrl();
    }
}