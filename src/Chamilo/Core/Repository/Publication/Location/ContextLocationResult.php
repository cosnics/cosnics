<?php
namespace Chamilo\Core\Repository\Publication\Location;

use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application\personal_calendar\integration\core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ContextLocationResult extends LocationResult
{

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_header()
     */
    public function get_header()
    {
        $headers = array();
        $headers[] = Translation :: get('Location', null, Manager :: context());
        return $headers;
    }

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_location()
     */
    public function get_location(LocationSupport $location)
    {
        $row = array();
        $row[] = $location->get_name();
        return $row;
    }
}