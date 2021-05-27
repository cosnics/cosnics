<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

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
        $glyph = new FontAwesomeGlyph('chart-pie', [], Translation::get('Details'));
        $count = 1;

        $conditions = [];
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
        $pub_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition
        );

        foreach($pub_resultset as $pub)
        {
            $params = [];
            $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
            $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $tool;
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] =
                $pub[ContentObjectPublication::PROPERTY_ID];
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
                \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;

            $detailParams = $params;
            $detailParams[Manager::PARAM_ACTION] =
                Manager::ACTION_VIEW_USER_PROGRESS;

            $link =
                '<a href="' . $this->get_parent()->get_url($detailParams) . '" target="_blank"">' . $glyph->render() .
                '</a>';

            $redirect = new Redirect($params);
            $url_title = $redirect->getUrl();

            $content_object = DataManager::retrieve_by_id(
                ContentObject::class, $pub[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
            );

            $reporting_data->add_category($count);
            $reporting_data->add_data_category_row(
                $count, Translation::get('Title'),
                '<a href="' . $url_title . '" target="_blank">' . $content_object->get_title() . '</a>'
            );
            $reporting_data->add_data_category_row($count, Translation::get('LearningPathDetails'), $link);

            $count ++;
        }
        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
