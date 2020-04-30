<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class MostActiveInactiveLastPublicationBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $courses = CourseDataManager::retrieves(Course::class, new DataClassRetrievesParameters());
        
        $arr[Translation::get('Past24hr')] = 0;
        $arr[Translation::get('PastWeek')] = 0;
        $arr[Translation::get('PastMonth')] = 0;
        $arr[Translation::get('PastYear')] = 0;
        $arr[Translation::get('NothingPublished')] = 0;
        $arr[Translation::get('MoreThenOneYear')] = 0;
        
        while ($course = $courses->next_result())
        {
            $lastpublication = 0;
            
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class,
                    ContentObjectPublication::PROPERTY_COURSE_ID), 
                new StaticConditionVariable($course->get_id()));
            $order_by = new OrderBy(
                new PropertyConditionVariable(
                    ContentObjectPublication::class,
                    ContentObjectPublication::PROPERTY_MODIFIED_DATE));
            $publications = DataManager::retrieve_content_object_publications(
                $condition, 
                $order_by, 
                0, 
                1);
            
            if ($publications->size() > 0)
            {
                $publication = $publications->next_result();
                $lastpublication = $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE];
            }
            
            if ($lastpublication == 0)
            {
                $arr[Translation::get('NothingPublished')] ++;
            }
            else
            {
                if ($lastpublication > time() - 86400)
                {
                    $arr[Translation::get('Past24hr')] ++;
                }
                else
                {
                    if ($lastpublication > time() - 604800)
                    {
                        $arr[Translation::get('PastWeek')] ++;
                    }
                    else
                    {
                        if ($lastpublication > time() - 18144000)
                        {
                            $arr[Translation::get('PastMonth')] ++;
                        }
                        else
                        {
                            if ($lastpublication > time() - 31536000)
                            {
                                $arr[Translation::get('PastYear')] ++;
                            }
                            else
                            {
                                $arr[Translation::get('MoreThenOneYear')] ++;
                            }
                        }
                    }
                }
            }
        }
        $reporting_data->set_categories(
            array(
                Translation::get('Past24hr'), 
                Translation::get('PastWeek'), 
                Translation::get('PastMonth'), 
                Translation::get('PastYear'), 
                Translation::get('MoreThenOneYear'), 
                Translation::get('NothingPublished')));
        $reporting_data->set_rows(array(Translation::get('count')));
        
        $reporting_data->add_data_category_row(
            Translation::get('Past24hr'), 
            Translation::get('count'), 
            $arr[Translation::get('Past24hr')]);
        $reporting_data->add_data_category_row(
            Translation::get('PastWeek'), 
            Translation::get('count'), 
            $arr[Translation::get('PastWeek')]);
        $reporting_data->add_data_category_row(
            Translation::get('PastMonth'), 
            Translation::get('count'), 
            $arr[Translation::get('PastMonth')]);
        $reporting_data->add_data_category_row(
            Translation::get('PastYear'), 
            Translation::get('count'), 
            $arr[Translation::get('PastYear')]);
        $reporting_data->add_data_category_row(
            Translation::get('NothingPublished'), 
            Translation::get('count'), 
            $arr[Translation::get('NothingPublished')]);
        $reporting_data->add_data_category_row(
            Translation::get('MoreThenOneYear'), 
            Translation::get('count'), 
            $arr[Translation::get('MoreThenOneYear')]);
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(
            Html::VIEW_TABLE,
            Html::VIEW_PIE);
    }
}
