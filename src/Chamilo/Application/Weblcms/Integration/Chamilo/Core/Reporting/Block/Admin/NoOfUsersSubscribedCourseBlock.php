<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

class NoOfUsersSubscribedCourseBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $users = \Chamilo\Core\User\Storage\DataManager :: count(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name());

        $courses = \Chamilo\Application\Weblcms\Storage\DataManager :: count(
            CourseUserRelation :: class_name(),
            new DataClassCountParameters(
                null,
                array(),
                new FunctionConditionVariable(
                    FunctionConditionVariable :: DISTINCT,
                    new PropertyConditionVariable(
                        CourseUserRelation :: class_name(),
                        CourseUserRelation :: PROPERTY_USER_ID))));

        $reporting_data->set_categories(
            array(Translation :: get('UsersSubscribedToCourse'), Translation :: get('UsersNotSubscribedToCourse')));
        $reporting_data->set_rows(array(Translation :: get('count')));

        $reporting_data->add_data_category_row(
            Translation :: get('UsersSubscribedToCourse'),
            Translation :: get('count'),
            $courses);
        $reporting_data->add_data_category_row(
            Translation :: get('UsersNotSubscribedToCourse'),
            Translation :: get('count'),
            $users - $courses);
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
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_PIE,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_BAR,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_LINE,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_STACKED_AREA);
    }
}
