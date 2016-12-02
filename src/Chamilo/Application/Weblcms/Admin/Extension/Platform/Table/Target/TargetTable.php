<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Target;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Entity\EntityTable;

/**
 * Table for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TargetTable extends EntityTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_ADMIN_ID;

    public function get_helper_class_name()
    {
        return $this->get_component()->get_selected_target_class(true);
    }
}