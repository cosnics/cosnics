<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\Location\ContextLocationResult;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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