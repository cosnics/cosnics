<?php
namespace Chamilo\Application\Survey\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\Location\ContextLocationResult;

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
    }
}