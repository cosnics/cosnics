<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
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

        $users = DataManager::count(User::class);

        $courses = DataManager::count(
            CourseEntityRelation::class, new DataClassParameters(
                condition: new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                    ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_USER)
                ), retrieveProperties: new RetrieveProperties(
                [
                    new FunctionConditionVariable(
                        FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                            CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                        )
                    )
                ]
            )
            )
        );

        $reporting_data->set_categories(
            [Translation::get('UsersSubscribedToCourse'), Translation::get('UsersNotSubscribedToCourse')]
        );
        $reporting_data->set_rows([Translation::get('count')]);

        $reporting_data->add_data_category_row(
            Translation::get('UsersSubscribedToCourse'), Translation::get('count'), $courses
        );
        $reporting_data->add_data_category_row(
            Translation::get('UsersNotSubscribedToCourse'), Translation::get('count'), $users - $courses
        );

        return $reporting_data;
    }

    public function get_views()
    {
        return [
            Html::VIEW_TABLE,
            Html::VIEW_PIE,
            Html::VIEW_BAR,
            Html::VIEW_LINE,
            Html::VIEW_STACKED_AREA
        ];
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
