<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\Location\ContextLocationResult;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package application\calendar\integration\core\repository\publication
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
        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Calendar\Manager :: context();
        $parameters[\Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_ACTION] = \Chamilo\Application\Calendar\Extension\Personal\Manager :: ACTION_VIEW;
        $parameters[\Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_PUBLICATION_ID] = $result->get_id();

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}