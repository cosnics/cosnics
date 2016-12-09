<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class AverageLearningPathScoreBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation::get('LearningPath')));
        
        $course_id = $this->get_course_id();
        
        $course = CourseDataManager::retrieve_by_id(Course::class_name(), $course_id);
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($course->get_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_TOOL), 
            new StaticConditionVariable('LearningPath'));
        
        $condition = new AndCondition($conditions);
        $lops = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications($condition);
        
        while ($lop = $lops->next_result())
        {
            $lpo = $lop->get_content_object();
            // $arr[$lpo->get_title()] = 0;
            $reporting_data->add_data_category_row(
                Translation::get('LearningPath'), 
                Translation::get('Average'), 
                $lpo->get_title());
        }
        
        // $datadescription[0] = Translation :: get('LearningPath');
        // $datadescription[1] = Translation :: get('Average');
        
        // $reporting_data->add_category($learn);
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_PIE);
    }
}
