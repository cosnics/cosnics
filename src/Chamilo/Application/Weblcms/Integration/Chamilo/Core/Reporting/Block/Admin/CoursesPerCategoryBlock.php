<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class CoursesPerCategoryBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('count')));
        
        $categories = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_course_categories_ordered_by_name();
        
        while ($category = $categories->next_result())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_CATEGORY_ID), 
                new StaticConditionVariable($category->get_id()));
            
            $reporting_data->add_category($category->get_name());
            $reporting_data->add_data_category_row(
                $category->get_name(), 
                Translation :: get('count'), 
                CourseDataManager :: count(Course :: class_name(), $condition));
        }
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_PIE);
    }
}
