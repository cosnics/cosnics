<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;

class CourseGroupUserTableCellRenderer extends DataClassTableCellRenderer
{

    /**
     * Renders a given cell.
     * 
     * @param type $column
     * @param type $user_with_subscription_status User from the advanced join query in weblcms database class that
     *        includes his subscription status.
     * @return type
     */
    public function render_cell($column, $user_with_subscription_status_and_type)
    {
        switch ($column->get_name())
        {
            
            case CourseGroup :: PROPERTY_ID :
                return DataManager :: get_course_groups_from_user_as_string(
                    $user_with_subscription_status_and_type->get_id(), 
                    $this->get_component()->get_course_id());
        }
        
        return parent :: render_cell($column, $user_with_subscription_status_and_type);
    }
}
