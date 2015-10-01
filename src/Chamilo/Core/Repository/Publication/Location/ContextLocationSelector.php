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
class ContextLocationSelector extends LocationSelector
{

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_header()
     */
    public function get_header()
    {
        $table_header = array();
        $table_header[] = '<th>';
        $table_header[] = Translation :: get('Location', null, Manager :: context());
        $table_header[] = '</th>';
        
        return implode('', $table_header);
    }

    /**
     *
     * @see \core\repository\publication\LocationRenderer::get_group()
     */
    public function get_group(LocationSupport $location)
    {
        $form_validator = $this->get_form_validator();
        $group = array();
        
        $group[] = $form_validator->createElement('static', null, null, $location->get_name());
        
        return $group;
    }
}