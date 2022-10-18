<?php
namespace Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;

/**
 * Cell renderer for the course group entity browser
 * 
 * @author Sven Vanpoucke
 */
class CourseGroupEntityTableColumnModel extends LocationEntityTableColumnModel
{
    const COLUMN_USERS = 'users';
    const COLUMN_SUBGROUPS = 'subgroups';

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_NAME));
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_DESCRIPTION));
        $this->addColumn(new StaticTableColumn(self::COLUMN_USERS));
        $this->addColumn(new StaticTableColumn(self::COLUMN_SUBGROUPS));
        
        parent::initializeColumns();
    }
}
