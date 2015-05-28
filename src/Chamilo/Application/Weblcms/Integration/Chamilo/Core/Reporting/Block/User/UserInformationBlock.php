<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\User;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;

class UserInformationBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $course_id = $this->get_course_id();
        $user_id = $this->get_user_id();
        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(), 
            $user_id);
        
        $course_summary_data = WeblcmsTrackingDataManager :: retrieve_course_access_summary_data($course_id, $user_id);
        
        $reporting_data->set_categories(
            array(Translation :: get('Name'), Translation :: get('Username'), Translation :: get('Email')));
        
        $this->add_reporting_data_categories_for_course_visit_data($reporting_data);
        $reporting_data->add_category(Translation :: get('TotalPublications'));
        
        $reporting_data->set_rows(array(Translation :: get('Details')));
        
        $reporting_data->add_data_category_row(
            Translation :: get('Name'), 
            Translation :: get('Details'), 
            $user->get_fullname());
        
        $reporting_data->add_data_category_row(
            Translation :: get('Username'), 
            Translation :: get('Details'), 
            $user->get_username());
        
        $reporting_data->add_data_category_row(
            Translation :: get('Email'), 
            Translation :: get('Details'), 
            '<a href="mailto:' . $user->get_email() . '" >' . $user->get_email() . '</a>');
        
        $this->add_reporting_data_from_course_visit_as_category(
            Translation :: get('Details'), 
            $reporting_data, 
            $course_summary_data);
        
        $reporting_data->add_data_category_row(
            Translation :: get('TotalPublications'), 
            Translation :: get('Details'), 
            $this->count_publications_from_user_in_course($user_id, $course_id));
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }
}
