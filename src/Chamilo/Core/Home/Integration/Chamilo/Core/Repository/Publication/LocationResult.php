<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\Location\ContextLocationResult;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * Displays a location where a content object is published
 *
 * @author Sven vanpoucke - Hogeschool Gent
 */
class LocationResult extends ContextLocationResult
{

    /**
     *
     * @see \core\repository\publication\LocationResult::get_link()
     */
    public function get_link(\Chamilo\Core\Repository\Publication\LocationSupport $location, $result)
    {
        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Home\Manager::context();
        $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Home\Manager::DEFAULT_ACTION;

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}