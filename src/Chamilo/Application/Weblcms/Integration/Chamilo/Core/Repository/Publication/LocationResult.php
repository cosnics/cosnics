<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\Location\LocationResult;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package personal_calendar\integration\core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LocationResult extends LocationResult
{

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_header()
     */
    public function get_header()
    {
        $headers = array();
        $headers[] = Translation :: get('Course', null, \Chamilo\Application\Weblcms\Manager :: context());
        $headers[] = Translation :: get('Tool', null, \Chamilo\Application\Weblcms\Manager :: context());
        return $headers;
    }

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_location()
     */
    public function get_location(LocationSupport $location)
    {
        $row = array();
        $row[] = $location->get_course_title() . ' (' . $location->get_visual_code() . ')';
        $row[] = $location->get_tool_name();
        return $row;
    }

    /**
     *
     * @see \core\repository\publication\LocationResult::get_link()
     */
    public function get_link(\Chamilo\Core\Repository\Publication\LocationSupport $location, $result)
    {
        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $location->get_course_id();
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $location->get_tool_id();
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $result->get_id();
        
        return Redirect :: get_url($parameters);
    }
}