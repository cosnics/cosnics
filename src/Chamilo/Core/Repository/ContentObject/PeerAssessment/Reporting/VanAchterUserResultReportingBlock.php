<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;

class VanAchterUserResultReportingBlock extends UserResultReportingBlock
{

    public function compose_data()
    {
        $publication_id = $this->get_parent()->get_parent()->get_publication_id();

        $indicators = $this->get_parent()->get_parent()->get_indicators();
        $processor = $this->get_parent()->get_parent()->get_root_content_object()->get_result_processor();
        $processor->retrieve_scores($this->get_parent()->get_parent(), $this->user->get_id(), $this->attempt->get_id());
        $scores = $processor->get_scores();

        foreach ($indicators as $indicator)
        {
            $rows[] = $indicator->get_title();
            $indicator_rows[$indicator->get_id()] = $indicator;
        }

        $rows[] = Translation::get('Total');

        unset($indicators);

        $reporting_data = new ReportingData();

        $reporting_data->set_rows($rows);

        $i = 0;

        foreach ($scores as $user_id => $indicators)
        {
            $i ++;

            if ($user_id == $this->user->get_id())
            {

                $user = $this->user;
            }
            else
            {
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                    (int) $user_id);
            }

            $reporting_data->add_category($i);

            // $reporting_data->add_data_category_row($i, Translation :: get('User', null, Utilities ::
            // COMMON_LIBRARIES) , $user->get_lastname() . ' ' . $user->get_firstname());

            foreach ($indicators as $indicator_id => $score)
            {
                $reporting_data->add_data_category_row($i, $indicator_rows[$indicator_id]->get_title(), $score);
                if (! isset($averages[$indicator_id]))
                    $averages[$indicator_id] = round($processor->col_avg($indicator_id), 2);
            }
            $reporting_data->add_data_category_row(
                $i,
                Translation::get('Total'),
                round($processor->row_sum($user_id), 2));
        }

        $i ++;
        $reporting_data->add_category(' ');
        foreach ($averages as $indicator_id => $average)
        {
            $reporting_data->add_data_category_row(' ', $indicator_rows[$indicator_id]->get_title(), $average);
        }

        $reporting_data->add_data_category_row(' ', Translation::get('Total'), round($processor->calculate(), 2));

//         $reporting_data->hide_categories();
        return $reporting_data;
    }
}
