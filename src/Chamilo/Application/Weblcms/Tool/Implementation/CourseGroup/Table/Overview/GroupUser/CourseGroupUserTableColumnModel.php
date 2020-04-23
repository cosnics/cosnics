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
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_FIRSTNAME));

        $showEmail = Configuration::getInstance()->get_setting(array('Chamilo\Core\User', 'show_email_addresses'));

        if($showEmail)
        {
            $this->add_column(new DataClassPropertyTableColumn(User::class_name(), User::PROPERTY_EMAIL));
        }

        $this->add_column(
            new DataClassPropertyTableColumn(
                CourseGroupUserRelation::class_name(), 
                CourseGroupUserRelation::PROPERTY_SUBSCRIPTION_TIME));
        
        // $title = Translation :: get(self :: COURSE_GROUP_COLOMN, array(), Utilities::COMMON_LIBRARIES);
        // // $this->add_column(
        // // new DataClassPropertyTableColumn(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID, $title, false));
    }
}
