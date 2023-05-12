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
use Chamilo\Libraries\Utilities\StringUtilities;

class LoginMonthBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $conditions = [];
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
        
        $data = Tracker::get_data(
            LoginLogout::class,
            Manager::CONTEXT,
            $condition);
        
        $months_names = array(
            Translation::get('JanuaryLong', null, StringUtilities::LIBRARIES),
            Translation::get('FebruaryLong', null, StringUtilities::LIBRARIES),
            Translation::get('MarchLong', null, StringUtilities::LIBRARIES),
            Translation::get('AprilLong', null, StringUtilities::LIBRARIES),
            Translation::get('MayLong', null, StringUtilities::LIBRARIES),
            Translation::get('JuneLong', null, StringUtilities::LIBRARIES),
            Translation::get('JulyLong', null, StringUtilities::LIBRARIES),
            Translation::get('AugustLong', null, StringUtilities::LIBRARIES),
            Translation::get('SeptemberLong', null, StringUtilities::LIBRARIES),
            Translation::get('OctoberLong', null, StringUtilities::LIBRARIES),
            Translation::get('NovemberLong', null, StringUtilities::LIBRARIES),
            Translation::get('DecemberLong', null, StringUtilities::LIBRARIES));
        $months = Block::getDateArray($data, 'n');
        
        $reporting_data->set_categories($months_names);
        $reporting_data->set_rows(array(Translation::get('Logins')));
        
        foreach ($months_names as $key => $name)
        {
            $reporting_data->add_data_category_row(
                $name, 
                Translation::get('Logins'), 
                ($months[$key + 1] ?: 0));
        }
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE, Html::VIEW_PIE, Html::VIEW_CSV, Html::VIEW_XLSX, Html::VIEW_XML);
    }
}
