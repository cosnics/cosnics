<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class LoginHourBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout :: class_name(),
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout :: PROPERTY_TYPE),
            new StaticConditionVariable('login'));
        $user_id = $this->get_user_id();
        if (isset($user_id))
        {
            $conditions[] = new EqualityCondition(
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout :: PROPERTY_USER_ID,
                $user_id);
        }
        $condition = new AndCondition($conditions);

        $data = Tracker :: get_data(
            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout :: CLASS_NAME,
            \Chamilo\Core\User\Manager :: context(),
            $condition);

        $hours = Block :: getDateArray($data, 'G');

        $hours_names = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24);
        $reporting_data->set_categories($hours_names);
        $reporting_data->set_rows(array(Translation :: get('Logins')));

        foreach ($hours_names as $key => $name)
        {
            $reporting_data->add_data_category_row(
                $name,
                Translation :: get('Logins'),
                ($hours[$key] ? $hours[$key] : 0));
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
            Html :: VIEW_TABLE,
            Html :: VIEW_PIE,
            Html :: VIEW_BAR,
            Html :: VIEW_LINE,
            Html :: VIEW_AREA,
            Html :: VIEW_CSV,
            Html :: VIEW_XLSX,
            Html :: VIEW_XML);
    }
}
