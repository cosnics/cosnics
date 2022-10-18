<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupUserRelation;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;

class CourseGroupUserTableColumnModel extends RecordTableColumnModel
{
    const COURSE_GROUP_COLOMN = 'CourseGroup';
    
    // **************************************************************************
    // CONSTRUCTOR
    // **************************************************************************
    
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

        $this->addColumn(
            new DataClassPropertyTableColumn(
                CourseGroupUserRelation::class,
                CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME));
        
        // $title = Translation::get(self::COURSE_GROUP_COLOMN, [], StringUtilities::LIBRARIES);
        // // $this->addColumn(
        // // new DataClassPropertyTableColumn(CourseGroup::class, CourseGroup::PROPERTY_ID, $title, false));
    }
}
