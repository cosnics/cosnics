<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\CourseUser;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Translation\Translation;

/**
 * Table column model for the course users table
 * 
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class CourseUsersTableColumnModel extends RecordTableColumnModel
{
    const COLUMN_COURSE_GROUPS = 'course_groups';
    const DEFAULT_ORDER_COLUMN_INDEX = 1;

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_EMAIL));
        }
        $this->addColumn(new StaticTableColumn(self::COLUMN_COURSE_GROUPS, Translation::get('CourseGroups')));
    }
}