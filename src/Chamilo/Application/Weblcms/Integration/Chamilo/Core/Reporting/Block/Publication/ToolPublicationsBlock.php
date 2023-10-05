<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\PublicationDetailTemplate;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ToolPublicationsBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $course_id = $this->getCourseId();
        $user_id = $this->get_user_id();
        $tool = $this->getRequest()->query->get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL
        );

        $reporting_data->set_rows([Translation::get('Title'), Translation::get('Description')]);

        $this->add_reporting_data_rows_for_course_visit_data($reporting_data);

        if (is_null($user_id))
        {
            $reporting_data->add_row(Translation::get('AccessDetails'));
        }

        $conditions = [];

        if ($user_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLISHER_ID
                ), new StaticConditionVariable($user_id)
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable($tool)
        );

        $condition = new AndCondition($conditions);

        $content_object_publications = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            ContentObjectPublication::class, new DataClassRetrievesParameters($condition)
        );

        $glyph = new FontAwesomeGlyph('chart-pie', [], Translation::get('Details'));

        $index = 1;
        foreach ($content_object_publications as $content_object_publication)
        {
            $params = [];

            $params[Application::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
            $params[Application::PARAM_CONTEXT] = Manager::CONTEXT;
            $params[Manager::PARAM_COURSE] = $course_id;
            $params[Manager::PARAM_TOOL] = $tool;
            $params[Manager::PARAM_PUBLICATION] = $content_object_publication->get_id();
            $params[Manager::PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;

            $url = $this->getUrlGenerator()->fromParameters($params);

            $content_object = DataManager::retrieve_by_id(
                ContentObject::class, (string) $content_object_publication->get_content_object_id()
            );

            $des = $content_object->get_description();
            //            $this->get_parent()->set_parameter($content_object_publication->get_id());
            $this->set_params($course_id, $user_id, $tool, $this->getPublicationId());

            $reporting_data->add_category($index);

            $reporting_data->add_data_category_row(
                $index, Translation::get('Title'), '<a href="' . $url . '">' . $content_object->get_title() . '</a>'
            );

            $reporting_data->add_data_category_row(
                $index, Translation::get('Description'), StringUtilities::getInstance()->truncate($des, 50)
            );

            $course_visit_data = $this->get_course_visit_summary_from_publication($content_object_publication);
            $this->add_reporting_data_from_course_visit_as_row($index, $reporting_data, $course_visit_data);

            if (is_null($user_id))
            {
                $params = $this->get_parent()->get_parameters();

                $params[Manager::PARAM_TEMPLATE_ID] = PublicationDetailTemplate::class;
                $params[Manager::PARAM_COURSE] = $course_id;
                $params[Manager::PARAM_USERS] = $user_id;
                $params[Manager::PARAM_PUBLICATION] = $content_object_publication->get_id();

                $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $glyph->render() . '</a>';

                $reporting_data->add_data_category_row($index, Translation::get('AccessDetails'), $link);
            }

            $index ++;
        }

        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function get_views()
    {
        return [Html::VIEW_TABLE];
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
