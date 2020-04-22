<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class PublicationDetailBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $course_id = $this->getCourseId();
        $tool = Request::get(\Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL);
        $pid = Request::get(Manager::PARAM_PUBLICATION_ID);
        
        $content_object_publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $pid);
        
        if (empty($content_object_publication))
        {
            $content_object_publication = DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $pid);
            $title = $content_object_publication->get_title();
            $id = $content_object_publication->get_id();
            $descr = $content_object_publication->get_description();
        }
        else
        {
            $title = $content_object_publication->get_content_object()->get_title();
            $id = $pid;
            $descr = $content_object_publication->get_content_object()->get_description();
        }
        
        $params = array();
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $tool;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $id;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = Manager::ACTION_VIEW;
        
        $redirect = new Redirect($params);
        $url = $redirect->getUrl();
        
        $reporting_data->set_categories(array(Translation::get('Title'), Translation::get('Description')));
        
        $this->add_reporting_data_categories_for_course_visit_data($reporting_data);
        
        $reporting_data->set_rows(array(Translation::get('count')));
        
        $reporting_data->add_data_category_row(
            Translation::get('Title'), 
            Translation::get('count'), 
            '<a href="' . $url . '">' . $title . '</a>');
        
        $reporting_data->add_data_category_row(
            Translation::get('Description'), 
            Translation::get('count'), 
            StringUtilities::getInstance()->truncate($descr, 50));
        
        $course_visit = $this->get_course_visit_summary_from_publication($content_object_publication);
        
        $this->add_reporting_data_from_course_visit_as_category(
            Translation::get('count'), 
            $reporting_data, 
            $course_visit);
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }
}
