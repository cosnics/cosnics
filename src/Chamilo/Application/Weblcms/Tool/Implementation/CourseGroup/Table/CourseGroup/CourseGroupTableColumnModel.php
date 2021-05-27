<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class CourseGroupTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_NUMBER_OF_MEMBERS = 'number_of_members';

    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_DESCRIPTION));

        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_NUMBER_OF_MEMBERS,
                Translation::getInstance()->getTranslation('NumberOfMembers', [], Manager::context())));

        $this->add_column(
            new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS));
    }
}
