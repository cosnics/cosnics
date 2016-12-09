<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class LoginDayBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::PROPERTY_TYPE), 
            new StaticConditionVariable('login'));
        $user_id = $this->get_user_id();
        
        if (isset($user_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
                    \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::PROPERTY_USER_ID), 
                new StaticConditionVariable($user_id));
        }
        
        $condition = new AndCondition($conditions);
        
        $data = DataManager::retrieves(
            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
            new DataClassRetrievesParameters($condition))->as_array();
        
        $days = array();
        foreach ($data as $date)
        {
            $day = date('N', $date->get_date());
            if (array_key_exists($day, $days))
            {
                $days[$day] ++;
            }
            else
            {
                $days[$day] = 1;
            }
        }
        $new_days = array();
        
        $day_names = array(
            Translation::get('MondayLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('TuesdayLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('WednesdayLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('ThursdayLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('FridayLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('SaturdayLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('SundayLong', null, Utilities::COMMON_LIBRARIES));
        
        $reporting_data->set_categories($day_names);
        $reporting_data->set_rows(array(Translation::get('Logins')));
        
        foreach ($day_names as $key => $name)
        {
            $reporting_data->add_data_category_row(
                $name, 
                Translation::get('Logins'), 
                ($days[$key + 1] ? $days[$key + 1] : 0));
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
            Html::VIEW_TABLE, 
            Html::VIEW_STACKED_AREA, 
            Html::VIEW_STACKED_BAR, 
            Html::VIEW_RADAR, 
            Html::VIEW_POLAR, 
            Html::VIEW_3D_PIE, 
            Html::VIEW_PIE, 
            Html::VIEW_RING, 
            Html::VIEW_BAR, 
            Html::VIEW_LINE, 
            Html::VIEW_AREA, 
            Html::VIEW_CSV, 
            Html::VIEW_XLSX, 
            Html::VIEW_XML);
    }
}
