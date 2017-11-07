<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Translation\Translation;

class NoOfUsersBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $reporting_data->set_categories(array(Translation::get('GetNumberOfUsers')));
        $reporting_data->set_rows(array(Translation::get('Count')));

        $reporting_data->add_data_category_row(
            Translation::get('GetNumberOfUsers'),
            Translation::get('Count'),
            \Chamilo\Core\User\Storage\DataManager::count(\Chamilo\Core\User\Storage\DataClass\User::class_name()));

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
