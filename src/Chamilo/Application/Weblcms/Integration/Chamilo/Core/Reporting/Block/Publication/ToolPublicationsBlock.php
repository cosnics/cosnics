<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\PublicationDetailTemplate;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class ToolPublicationsBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $course_id = $this->get_course_id();
        $user_id = $this->get_user_id();
        $tool = Request :: get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager :: PARAM_REPORTING_TOOL);

        $reporting_data->set_rows(array(Translation :: get('Title'), Translation :: get('Description')));

        $this->add_reporting_data_rows_for_course_visit_data($reporting_data);

        if (is_null($user_id))
        {
            $reporting_data->add_row(Translation :: get('AccessDetails'));
        }

        $conditions = array();

        if ($user_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_PUBLISHER_ID),
                new StaticConditionVariable($user_id));
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TOOL),
            new StaticConditionVariable($tool));

        $condition = new AndCondition($conditions);

        $content_object_publications = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublication :: class_name(),
            $condition);

        $img = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Reporting') . '" title="' .
             Translation :: get('Details') . '" />';

        $index = 1;
        while ($content_object_publication = $content_object_publications->next_result())
        {
            $params = array();

            $params[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
            $params[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $course_id;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $tool;
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $content_object_publication->get_id();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;

            $url = Redirect :: get_url($params);

            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                $content_object_publication->get_content_object_id());

            $des = $content_object->get_description();
            $this->get_parent()->set_parameter($content_object_publication->get_id());
            $this->set_params($course_id, $user_id, $tool, $this->get_publication_id());

            $reporting_data->add_category($index);

            $reporting_data->add_data_category_row(
                $index,
                Translation :: get('Title'),
                '<a href="' . $url . '">' . $content_object->get_title() . '</a>');

            $reporting_data->add_data_category_row(
                $index,
                Translation :: get('Description'),
                Utilities :: truncate_string($des, 50));

            $course_visit_data = $this->get_course_visit_summary_from_publication($content_object_publication);
            $this->add_reporting_data_from_course_visit_as_row($index, $reporting_data, $course_visit_data);

            if (is_null($user_id))
            {
                $params = $this->get_parent()->get_parameters();

                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID] = PublicationDetailTemplate :: class_name();
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $course_id;
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = $user_id;
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $content_object_publication->get_id();

                $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $img . '</a>';

                $reporting_data->add_data_category_row($index, Translation :: get('AccessDetails'), $link);
            }

            $index ++;
        }

        $reporting_data->hide_categories();
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
