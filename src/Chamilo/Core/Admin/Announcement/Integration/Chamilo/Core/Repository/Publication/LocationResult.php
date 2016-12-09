<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication;

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
        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Core\Admin\Manager :: context();
        $parameters[\Chamilo\Core\Admin\Manager :: PARAM_ACTION] = \Chamilo\Core\Admin\Manager :: ACTION_SYSTEM_ANNOUNCEMENTS;
        $parameters[\Chamilo\Core\Admin\Announcement\Manager :: PARAM_ACTION] = \Chamilo\Core\Admin\Announcement\Manager :: ACTION_VIEW;
        $parameters[\Chamilo\Core\Admin\Announcement\Manager :: PARAM_SYSTEM_ANNOUNCEMENT_ID] = $result->get_id();

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}