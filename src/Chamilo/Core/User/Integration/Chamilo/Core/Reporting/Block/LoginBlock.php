<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class LoginBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LoginLogout::class,
                LoginLogout::PROPERTY_TYPE),
            new StaticConditionVariable('login'));
        $user_id = $this->get_user_id();
        if (isset($user_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    LoginLogout::class,
                    LoginLogout::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));
        }
        $condition = new AndCondition($conditions);
        
        $count = Tracker::count_data(
            LoginLogout::class,
            Manager::context(),
            $condition);
        
        $reporting_data->set_categories(array(Translation::get('Logins')));
        $reporting_data->set_rows(array(Translation::get('Count')));
        
        $reporting_data->add_data_category_row(Translation::get('Logins'), Translation::get('Count'), $count);
        
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
            Html::VIEW_CSV,
            Html::VIEW_XLSX,
            Html::VIEW_XML);
    }
}
