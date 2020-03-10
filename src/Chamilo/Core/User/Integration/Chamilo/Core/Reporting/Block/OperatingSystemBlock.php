<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\OperatingSystem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class OperatingSystemBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $tracker = new OperatingSystem();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                OperatingSystem::class_name(),
                OperatingSystem::PROPERTY_TYPE),
            new StaticConditionVariable('os'));
        $description[0] = Translation::get('Os');
        
        $data = Block::array_from_tracker($tracker, $condition, $description);
        $keys = array_keys($data);
        $reporting_data->set_categories($keys);
        $reporting_data->set_rows(array(Translation::get('Os')));
        
        foreach ($keys as $key => $name)
        {
            $reporting_data->add_data_category_row($name, Translation::get('Os'), $data[$name]);
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
