<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;

class PublicationUserAccessBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $content_object_publication_id = $this->getPublicationId();
        $content_object_publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $content_object_publication_id);
        
        $reporting_data->set_categories(
            array(
                Translation::get('User', null, \Chamilo\Core\User\Manager::context()), 
                Translation::get('OfficialCode', null, \Chamilo\Core\User\Manager::context())));
        
        $this->add_reporting_data_categories_for_course_visit_data($reporting_data);
        
        $reporting_data->set_rows(array(Translation::get('count')));
        
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class_name(), $this->get_user_id());
        
        $reporting_data->add_data_category_row(
            Translation::get('User', null, \Chamilo\Core\User\Manager::context()), 
            Translation::get('count'), 
            $user->get_fullname());
        
        $reporting_data->add_data_category_row(
            Translation::get('OfficialCode', null, \Chamilo\Core\User\Manager::context()), 
            Translation::get('count'), 
            $user->get_official_code());
        
        $course_visit = $this->get_course_visit_summary_from_publication($content_object_publication);
        
        $this->add_reporting_data_from_course_visit_as_row(Translation::get('count'), $reporting_data, $course_visit);
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
