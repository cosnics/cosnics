<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class NoOfUsersSubscribedCourseBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $users = DataManager::count(User::class, new DataClassCountParameters());

        $courses = DataManager::count(
            CourseEntityRelation::class,
            new DataClassCountParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class,
                        CourseEntityRelation::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)),
                null,
                new RetrieveProperties(
                    array(
                        new FunctionConditionVariable(
                            FunctionConditionVariable::DISTINCT,
                            new PropertyConditionVariable(
                                CourseEntityRelation::class,
                                CourseEntityRelation::PROPERTY_ENTITY_ID))))));

        $reporting_data->set_categories(
            array(Translation::get('UsersSubscribedToCourse'), Translation::get('UsersNotSubscribedToCourse')));
        $reporting_data->set_rows(array(Translation::get('count')));

        $reporting_data->add_data_category_row(
            Translation::get('UsersSubscribedToCourse'),
            Translation::get('count'),
            $courses);
        $reporting_data->add_data_category_row(
            Translation::get('UsersNotSubscribedToCourse'),
            Translation::get('count'),
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
            Html::VIEW_TABLE,
            Html::VIEW_PIE,
            Html::VIEW_BAR,
            Html::VIEW_LINE,
            Html::VIEW_STACKED_AREA);
    }
}
