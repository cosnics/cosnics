<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying all learning paths within a course and
 *          their attempt stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class LearningPathBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation::get('Title'),
                Translation::get('LearningPathDetails')
            )
        );

        $course_id = $this->getCourseId();
        $tool = ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
            \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::package()
        );
        $img = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Reporting') . '" title="' .
            Translation::get('Details') . '" />';
        $count = 1;

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_COURSE_ID
            ),
            new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_TOOL
            ),
            new StaticConditionVariable($tool)
        );
        $condition = new AndCondition($conditions);
        $pub_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition
        );

        while ($pub = $pub_resultset->next_result())
        {
            $params = array();
            $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
            $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $tool;
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] =
                $pub[ContentObjectPublication::PROPERTY_ID];
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
                \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;

            $detailParams = $params;
            $detailParams[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION] =
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS;

            $link = '<a href="' . $this->get_parent()->get_url($detailParams) . '" target="_blank"">' . $img . '</a>';

            $redirect = new Redirect($params);
            $url_title = $redirect->getUrl();

            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $pub[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
            );

            $reporting_data->add_category($count);
            $reporting_data->add_data_category_row(
                $count,
                Translation::get('Title'),
                '<a href="' . $url_title . '" target="_blank">' . $content_object->get_title() . '</a>'
            );
            $reporting_data->add_data_category_row($count, Translation::get('LearningPathDetails'), $link);

            $count ++;
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
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
