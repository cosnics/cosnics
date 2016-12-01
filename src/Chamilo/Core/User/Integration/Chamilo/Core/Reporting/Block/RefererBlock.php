<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class RefererBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $tracker = new \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Referrer();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Provider::class_name(), 
                \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Referrer::PROPERTY_TYPE), 
            new StaticConditionVariable('referer'));
        $description[0] = Translation::get('Referers');
        
        $data = Block::array_from_tracker($tracker, $condition, $description);
        $keys = array_keys($data);
        $reporting_data->set_categories($keys);
        $reporting_data->set_rows(array(Translation::get('Referers')));
        
        foreach ($keys as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation::get('Referers'), $data[$name]);
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
            Html::VIEW_PIE, 
            Html::VIEW_BAR, 
            Html::VIEW_LINE, 
            Html::VIEW_AREA, 
            Html::VIEW_CSV, 
            Html::VIEW_XLSX, 
            Html::VIEW_XML);
    }
}
