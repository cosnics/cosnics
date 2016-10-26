<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class ActiveInactiveBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $users = \Chamilo\Core\User\Storage\DataManager :: retrieves(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            new DataClassRetrievesParameters());
        $active[Translation :: get('Active')] = 0;
        $active[Translation :: get('Inactive')] = 0;
        while ($user = $users->next_result())
        {
            if ($user->get_active())
            {
                $active[Translation :: get('Active')] ++;
            }
            else
            {
                $active[Translation :: get('Inactive')] ++;
            }
        }
        $reporting_data->set_categories(array(Translation :: get('Active'), Translation :: get('Inactive')));
        $reporting_data->set_rows(array(Translation :: get('Count')));

        $reporting_data->add_data_category_row(
            Translation :: get('Active'),
            Translation :: get('Count'),
            $active[Translation :: get('Active')]);
        $reporting_data->add_data_category_row(
            Translation :: get('Inactive'),
            Translation :: get('Count'),
            $active[Translation :: get('Inactive')]);
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
            Html :: VIEW_STACKED_AREA,
            Html :: VIEW_STACKED_BAR,
            Html :: VIEW_RADAR,
            Html :: VIEW_POLAR,
            Html :: VIEW_3D_PIE,
            Html :: VIEW_PIE,
            Html :: VIEW_RING,
            Html :: VIEW_BAR,
            Html :: VIEW_LINE,
            Html :: VIEW_AREA,
            Html :: VIEW_CSV,
            Html :: VIEW_XLSX,
            Html :: VIEW_XML);
    }
}
