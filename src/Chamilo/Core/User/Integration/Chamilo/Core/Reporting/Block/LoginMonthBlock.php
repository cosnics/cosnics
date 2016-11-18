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
use Chamilo\Libraries\Utilities\Utilities;

class LoginMonthBlock extends Block
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
        
        $data = Tracker::get_data(
            \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\LoginLogout::class_name(), 
            \Chamilo\Core\User\Manager::context(), 
            $condition);
        
        $months_names = array(
            Translation::get('JanuaryLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('FebruaryLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('MarchLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('AprilLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('MayLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('JuneLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('JulyLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('AugustLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('SeptemberLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('OctoberLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('NovemberLong', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('DecemberLong', null, Utilities::COMMON_LIBRARIES));
        $months = Block::getDateArray($data, 'n');
        
        $reporting_data->set_categories($months_names);
        $reporting_data->set_rows(array(Translation::get('Logins')));
        
        foreach ($months_names as $key => $name)
        {
            $reporting_data->add_data_category_row(
                $name, 
                Translation::get('Logins'), 
                ($months[$key + 1] ? $months[$key + 1] : 0));
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
