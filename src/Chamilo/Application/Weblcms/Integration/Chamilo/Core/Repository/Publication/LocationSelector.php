<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package personal_calendar\integration\core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LocationSelector extends \Chamilo\Core\Repository\Publication\Location\LocationSelector
{

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_header()
     */
    public function get_header()
    {
        $table_header = array();
        $table_header[] = '<th>' . Translation::get('Course', null, \Chamilo\Application\Weblcms\Manager::context()) .
             '</th>';
        $table_header[] = '<th>' . Translation::get('Tool', null, \Chamilo\Application\Weblcms\Manager::context()) .
             '</th>';
        return implode(PHP_EOL, $table_header);
    }

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_group()
     */
    public function get_group(LocationSupport $location)
    {
        $form_validator = $this->get_form_validator();
        $group = array();
        
        $group[] = $form_validator->createElement(
            'static', 
            null, 
            null, 
            $location->get_course_title() . ' (' . $location->get_visual_code() . ')');
        $group[] = $form_validator->createElement('static', null, null, $location->get_tool_name());
        
        return $group;
    }
}