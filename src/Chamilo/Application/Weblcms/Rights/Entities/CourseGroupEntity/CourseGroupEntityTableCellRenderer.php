<?php
namespace Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableCellRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Cell renderer for the course group entity browser
 * 
 * @author Sven Vanpoucke
 */
class CourseGroupEntityTableCellRenderer extends LocationEntityTableCellRenderer
{

    /**
     * Renders the cell for the given column and course group
     * 
     * @param $column LocationCourseGroupBrowserTableColumnModel
     * @param $entity_item CourseGroup
     * @return String
     */
    public function render_cell($column, $entity_item)
    {
        switch ($column->get_name())
        {
            case CourseGroup::PROPERTY_DESCRIPTION :
                $description = StringUtilities::getInstance()->truncate($entity_item->get_description(), 50);
                return StringUtilities::getInstance()->truncate($description);
            case CourseGroupEntityTableColumnModel::COLUMN_USERS :
                return $entity_item->count_members();
            case CourseGroupEntityTableColumnModel::COLUMN_SUBGROUPS :
                return $entity_item->count_children(true);
        }
        
        return parent::render_cell($column, $entity_item);
    }
}
